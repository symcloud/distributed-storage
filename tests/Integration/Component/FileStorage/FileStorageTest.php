<?php

namespace Integration\Component\FileStorage;

use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\Policy;

class FileStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobFileManagerTrait;

    public function storageProvider()
    {
        $factory = $this->getFactory();

        $size = 200;
        $mimeType = 'application/json';
        list($data, $fileName) = $this->generateTestFile($size);
        $blobs = array(
            $factory->createBlob(substr($data, 0, 100)),
            $factory->createBlob(substr($data, 100, 100))
        );
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

        $this->assertEquals($factory->createHash($data), $result->getHash());
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
        $this->assertEquals($fileHash, $model->getHash());
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
        $file->setHash($fileHash);
        $file->setBlobs($blobs);
        $file->setPolicy(new Policy());
        $file->setMimetype($mimeType);
        $file->setSize($size);

        $blobManager = $this->getBlobManager();
        $manager = $this->getBlobFileManager();
        $database = $this->getDatabase();

        $blobManager->upload($blobs[0]->getData());
        $blobManager->upload($blobs[1]->getData());
        $database->store($file);

        $result = $manager->download($fileHash);

        $this->assertEquals($fileHash, $result->getHash());
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
        $this->assertEquals($fileHash, $model->getHash());
        $this->assertEquals($data, $model->getContent());
        $this->assertEquals($blobs[0]->getHash(), $model->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $model->getBlobs()[1]->getHash());
        $this->assertEquals(strlen($data), $model->getSize());
        $this->assertEquals($size, $model->getSize());
        $this->assertEquals($mimeType, $model->getMimetype());
    }
}
