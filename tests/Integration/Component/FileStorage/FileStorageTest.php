<?php

namespace Integration\Component\FileStorage;

use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;

class FileStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobFileManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobNamespace());
        $this->clearBucket($this->getBlobFileNamespace());

        parent::setUp();
    }

    public function storageProvider()
    {
        $factory = $this->getFactory();

        list($data, $fileName) = $this->generateTestFile(200);
        $blobs = array(
            $factory->createBlob(substr($data, 0, 100)),
            $factory->createBlob(substr($data, 100, 100))
        );
        $fileHash = $factory->createFileHash($fileName);

        return array(
            array(
                $this->getBlobFileManager(),
                $fileName,
                $data,
                $fileHash,
                $blobs,
                $this->getBlobFileNamespace(),
                $this->getBlobNamespace(),
                $factory
            )
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobFileManagerInterface $manager
     * @param string $fileName
     * @param string $data
     * @param string $fileHash
     * @param BlobInterface[] $blobs
     * @param RiakNamespace $fileNamespace
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testUpload(
        BlobFileManagerInterface $manager,
        $fileName,
        $data,
        $fileHash,
        $blobs,
        RiakNamespace $fileNamespace,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $result = $manager->upload($fileName);

        $this->assertEquals($factory->createHash($data), $result->getHash());
        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());
        $this->assertEquals($data, $result->getContent());

        $fileKeys = $this->fetchBucketKeys($fileNamespace);
        $blobKeys = $this->fetchBucketKeys($blobNamespace);

        $this->assertContains($result->getBlobs()[0]->getHash(), $blobKeys);
        $this->assertContains($result->getBlobs()[1]->getHash(), $blobKeys);
        $this->assertNotContains($result->getBlobs()[0]->getHash(), $fileKeys);
        $this->assertNotContains($result->getBlobs()[1]->getHash(), $fileKeys);

        $this->assertContains($result->getHash(), $fileKeys);
        $this->assertNotContains($result->getHash(), $blobKeys);

        $this->assertEquals(
            $blobs[0]->getData(),
            $this->fetchObject($blobs[0]->getHash(), $blobNamespace)->getValue()->getValue()->getContents()
        );
        $this->assertEquals(
            $blobs[1]->getData(),
            $this->fetchObject($blobs[1]->getHash(), $blobNamespace)->getValue()->getValue()->getContents()
        );

        $this->assertEquals(
            array($blobs[0]->getHash(), $blobs[1]->getHash()),
            json_decode($this->fetchObject($fileHash, $fileNamespace)->getValue()->getValue(), true)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobFileManagerInterface $manager
     * @param string $fileName
     * @param string $data
     * @param string $fileHash
     * @param BlobInterface[] $blobs
     * @param RiakNamespace $fileNamespace
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testDownload(
        BlobFileManagerInterface $manager,
        $fileName,
        $data,
        $fileHash,
        $blobs,
        RiakNamespace $fileNamespace,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $this->storeObject($blobs[0]->getHash(), $blobs[0]->getData(), $blobNamespace);
        $this->storeObject($blobs[1]->getHash(), $blobs[1]->getData(), $blobNamespace);
        $this->storeObject($fileHash, array($blobs[0]->getHash(), $blobs[1]->getHash()), $fileNamespace);

        $result = $manager->download($fileHash);

        $this->assertEquals($result->getHash(), $result->getHash());
        $this->assertEquals($result->getContent(), $data);

        $this->assertCount(count($blobs), $result->getBlobs());

        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());

        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());

        $fileKeys = $this->fetchBucketKeys($fileNamespace);
        $blobKeys = $this->fetchBucketKeys($blobNamespace);

        $this->assertContains($result->getBlobs()[0]->getHash(), $blobKeys);
        $this->assertContains($result->getBlobs()[1]->getHash(), $blobKeys);
        $this->assertNotContains($result->getBlobs()[0]->getHash(), $fileKeys);
        $this->assertNotContains($result->getBlobs()[1]->getHash(), $fileKeys);

        $this->assertContains($result->getHash(), $fileKeys);
        $this->assertNotContains($result->getHash(), $blobKeys);
    }
}
