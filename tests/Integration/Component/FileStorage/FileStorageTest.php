<?php

namespace Integration\Component\FileStorage;

use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
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
    )
    {
        $manager = $this->getBlobFileManager();
        $database = $this->getDatabase();
        $factory = $this->getFactory();

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertNotNull($result->getHash());
        $this->assertEquals($factory->createHash($data), $result->getHash());
        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
        $this->assertEquals($data, $result->getContent());

        $this->assertTrue($database->contains($result->getBlobs()[0]->getHash(), Blob::class));
        $this->assertTrue($database->contains($result->getBlobs()[1]->getHash(), Blob::class));

        $this->assertEquals(
            $blobs[0]->getData(),
            $database->fetch($blobs[0]->getHash(), Blob::class)->getData()
        );
        $this->assertEquals(
            $blobs[1]->getData(),
            $database->fetch($blobs[1]->getHash(), Blob::class)->getData()
        );
    }

    protected function createSearchAdapter()
    {
        $this->searchAdapterMock = $this->prophesize(SearchAdapterInterface::class);

        return $this->searchAdapterMock->reveal();
    }
}
