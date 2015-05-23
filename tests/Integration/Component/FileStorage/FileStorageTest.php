<?php

namespace Integration\Component\FileStorage;

use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Search\Hit\Hit;
use Symcloud\Component\Database\Search\SearchAdapterInterface;

class FileStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobFileManagerTrait;

    private $searchAdapterMock;

    public function storageProvider()
    {
        $factory = $this->getFactory();

        $size = 200;
        $mimeType = 'application/json';
        list($data, $fileName) = $this->generateTestFile($size);
        $blob1 = new Blob();
        $blob1->setData(substr($data, 0, 100));
        $blob1->setHash($factory->createHash($blob1->getData()));
        $blob1->setLength(strlen($blob1->getData()));

        $blob2 = new Blob();
        $blob2->setData(substr($data, 100, 100));
        $blob2->setHash($factory->createHash($blob2->getData()));
        $blob2->setLength(strlen($blob2->getData()));

        $blobs = array($blob1, $blob2);
        $fileHash = $factory->createFileHash($fileName);

        return array(
            array(
                $fileName,
                $data,
                $fileHash,
                $blobs,
                $mimeType,
                $size
            )
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param string $fileName
     * @param string $data
     * @param string $fileHash
     * @param BlobInterface[] $blobs
     * @param $mimeType
     * @param $size
     */
    public function testUpload(
        $fileName,
        $data,
        $fileHash,
        $blobs,
        $mimeType,
        $size
    ) {
        $manager = $this->getBlobFileManager();
        $database = $this->getDatabase();
        $factory = $this->getFactory();

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertNotNull($result->getHash());
        $this->assertEquals($factory->createHash($data), $result->getFileHash());
        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
        $this->assertEquals($data, $result->getContent());

        $this->assertTrue($database->contains($result->getBlobs()[0]->getHash()));
        $this->assertTrue($database->contains($result->getBlobs()[1]->getHash()));
        $this->assertTrue($database->contains($result->getHash()));

        $this->assertEquals(
            $blobs[0]->getData(),
            $database->fetch($blobs[0]->getHash())->getData()
        );
        $this->assertEquals(
            $blobs[1]->getData(),
            $database->fetch($blobs[1]->getHash())->getData()
        );

        /** @var BlobFileInterface $model */
        $model = $database->fetch($result->getHash());
        $this->assertEquals($result->getHash(), $model->getHash());
        $this->assertEquals($fileHash, $model->getFileHash());
        $this->assertEquals($data, $model->getContent());
        $this->assertEquals($blobs[0]->getHash(), $model->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $model->getBlobs()[1]->getHash());
        $this->assertEquals(strlen($data), $model->getSize());
        $this->assertEquals($size, $model->getSize());
        $this->assertEquals($mimeType, $model->getMimetype());
    }

    /**
     * @dataProvider storageProvider
     *
     * @param string $fileName
     * @param string $data
     * @param string $fileHash
     * @param BlobInterface[] $blobs
     * @param $mimeType
     * @param $size
     */
    public function testDownload(
        $fileName,
        $data,
        $fileHash,
        $blobs,
        $mimeType,
        $size
    ) {
        $file = new BlobFile();
        $file->setPolicy(new Policy());
        $file->setFileHash($fileHash);
        $file->setMimetype($mimeType);
        $file->setBlobs($blobs);
        $file->setSize($size);

        $blobManager = $this->getBlobManager();
        $manager = $this->getBlobFileManager();
        $database = $this->getDatabase();

        $blobManager->upload($blobs[0]->getData());
        $blobManager->upload($blobs[1]->getData());
        $database->store($file);

        $hit = new Hit();
        $hit->setHash($file->getHash());
        $hits = array($hit);
        $this->searchAdapterMock->search('fileHash:'.$fileHash, array('file'))->willReturn($hits);

        $result = $manager->download($fileHash);

        $this->assertNotNull($result->getHash());
        $this->assertEquals($fileHash, $result->getFileHash());
        $this->assertEquals($data, $result->getContent());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());

        $this->assertCount(count($blobs), $result->getBlobs());
        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());

        $this->assertTrue($database->contains($result->getBlobs()[0]->getHash()));
        $this->assertTrue($database->contains($result->getBlobs()[1]->getHash()));
        $this->assertTrue($database->contains($result->getHash()));

        $this->assertEquals(
            $blobs[0]->getData(),
            $database->fetch($blobs[0]->getHash())->getData()
        );
        $this->assertEquals(
            $blobs[1]->getData(),
            $database->fetch($blobs[1]->getHash())->getData()
        );

        /** @var BlobFileInterface $model */
        $model = $database->fetch($result->getHash());
        $this->assertEquals($result->getHash(), $model->getHash());
        $this->assertEquals($fileHash, $model->getFileHash());
        $this->assertEquals($data, $model->getContent());
        $this->assertEquals($blobs[0]->getHash(), $model->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $model->getBlobs()[1]->getHash());
        $this->assertEquals(strlen($data), $model->getSize());
        $this->assertEquals($size, $model->getSize());
        $this->assertEquals($mimeType, $model->getMimetype());
    }

    protected function createSearchAdapter()
    {
        $this->searchAdapterMock = $this->prophesize(SearchAdapterInterface::class);

        return $this->searchAdapterMock->reveal();
    }
}
