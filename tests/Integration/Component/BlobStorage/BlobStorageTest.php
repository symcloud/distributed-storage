<?php

namespace Integration\Component\BlobStorage;

use Basho\Riak;
use Basho\Riak\Bucket;
use Integration\Parts\BlobManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;

class BlobStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobBucket());

        parent::setUp();
    }

    public function storageProvider()
    {
        $length = 200;
        $data = $this->generateString($length);

        $expectedBlob = $this->getFactory()->createBlob($data);

        return array(
            array($this->getBlobManager(), $expectedBlob, $this->getBlobBucket(), $this->getRiak())
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobManager
     * @param BlobInterface $expectedBlob
     * @param Bucket $blobBucket
     * @param Riak $riak
     */
    public function testUpload(
        BlobManagerInterface $blobManager,
        BlobInterface $expectedBlob,
        Bucket $blobBucket,
        Riak $riak
    ) {
        $blob = $blobManager->uploadBlob($expectedBlob->getData());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $riakResponse = $this->fetchObject($blob->getHash(), $blobBucket, $riak);
        $this->assertEquals($expectedBlob->getData(), $riakResponse->getObject()->getData());

        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;
        $this->assertContains($blob->getHash(), $blobKeys);
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobManager
     * @param BlobInterface $expectedBlob
     * @param Bucket $blobBucket
     * @param Riak $riak
     */
    public function testDownload(
        BlobManagerInterface $blobManager,
        BlobInterface $expectedBlob,
        Bucket $blobBucket,
        Riak $riak
    ) {
        $this->storeObject($expectedBlob->getHash(), $expectedBlob->getData(), $blobBucket, $riak);

        $blob = $blobManager->downloadBlob($expectedBlob->getHash());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;
        $this->assertContains($blob->getHash(), $blobKeys);
    }
}
