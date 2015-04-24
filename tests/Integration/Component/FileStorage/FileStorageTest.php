<?php

namespace Integration\Component\FileStorage;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;

class FileStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobFileManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobBucket());
        $this->clearBucket($this->getBlobFileBucket());

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
                $this->getBlobFileBucket(),
                $this->getBlobBucket(),
                $this->getRiak(),
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
     * @param Bucket $fileBucket
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     * @param Riak $riak
     */
    public function testUpload(
        BlobFileManagerInterface $manager,
        $fileName,
        $data,
        $fileHash,
        $blobs,
        Bucket $fileBucket,
        Bucket $blobBucket,
        Riak $riak,
        FactoryInterface $factory
    ) {
        $result = $manager->upload($fileName);

        $this->assertEquals($factory->createHash($data), $result->getHash());
        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());
        $this->assertEquals($data, $result->getContent());

        $fileKeys = $this->fetchBucketKeys($fileBucket, $riak)->getObject()->getData()->keys;
        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;

        $this->assertContains($result->getBlobs()[0]->getHash(), $blobKeys);
        $this->assertContains($result->getBlobs()[1]->getHash(), $blobKeys);
        $this->assertNotContains($result->getBlobs()[0]->getHash(), $fileKeys);
        $this->assertNotContains($result->getBlobs()[1]->getHash(), $fileKeys);

        $this->assertContains($result->getHash(), $fileKeys);
        $this->assertNotContains($result->getHash(), $blobKeys);

        $this->assertEquals(
            $blobs[0]->getData(),
            $this->fetchObject($blobs[0]->getHash(), $blobBucket, $riak)->getObject()->getData()
        );
        $this->assertEquals(
            $blobs[1]->getData(),
            $this->fetchObject($blobs[1]->getHash(), $blobBucket, $riak)->getObject()->getData()
        );

        $this->assertEquals(
            array($blobs[0]->getHash(), $blobs[1]->getHash()),
            $this->fetchObject($fileHash, $fileBucket, $riak)->getObject()->getData()
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
     * @param Bucket $fileBucket
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     * @param Riak $riak
     */
    public function testDownload(
        BlobFileManagerInterface $manager,
        $fileName,
        $data,
        $fileHash,
        $blobs,
        Bucket $fileBucket,
        Bucket $blobBucket,
        Riak $riak,
        FactoryInterface $factory
    ) {
        $this->storeObject($blobs[0]->getHash(), $blobs[0]->getData(), $blobBucket, $riak);
        $this->storeObject($blobs[1]->getHash(), $blobs[1]->getData(), $blobBucket, $riak);
        $this->storeObject($fileHash, array($blobs[0]->getHash(), $blobs[1]->getHash()), $fileBucket, $riak);

        $result = $manager->download($fileHash);

        $this->assertEquals($result->getHash(), $result->getHash());
        $this->assertEquals($result->getContent(), $data);

        $this->assertCount(count($blobs), $result->getBlobs());

        $this->assertEquals($blobs[0]->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blobs[1]->getHash(), $result->getBlobs()[1]->getHash());

        $this->assertEquals($blobs[0]->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blobs[1]->getData(), $result->getBlobs()[1]->getData());

        $fileKeys = $this->fetchBucketKeys($fileBucket, $riak)->getObject()->getData()->keys;
        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;

        $this->assertContains($result->getBlobs()[0]->getHash(), $blobKeys);
        $this->assertContains($result->getBlobs()[1]->getHash(), $blobKeys);
        $this->assertNotContains($result->getBlobs()[0]->getHash(), $fileKeys);
        $this->assertNotContains($result->getBlobs()[1]->getHash(), $fileKeys);

        $this->assertContains($result->getHash(), $fileKeys);
        $this->assertNotContains($result->getHash(), $blobKeys);
    }
}
