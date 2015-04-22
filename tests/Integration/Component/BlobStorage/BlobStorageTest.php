<?php

namespace Integration\Component\BlobStorage;

use Basho\Riak;
use Basho\Riak\Bucket;
use Integration\BaseIntegrationTest;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Riak\RiakBlobAdapter;

class BlobStorageTest extends BaseIntegrationTest
{
    public function storageProvider()
    {
        $factory = $this->getFactory();
        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();

        $this->clearBucket($blobBucket, $riak);

        $blobAdapter = new RiakBlobAdapter($riak, $blobBucket);
        $blobStorage = new BlobManager($factory, $blobAdapter);

        $length = 200;
        $data = $this->generateString($length);

        $expectedBlob = $factory->createBlob($data);

        return array(
            array($blobStorage, $expectedBlob, $blobBucket, $riak)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobStorage
     * @param BlobInterface $expectedBlob
     * @param Bucket $blobBucket
     * @param Riak $riak
     */
    public function testUpload(
        BlobManagerInterface $blobStorage,
        BlobInterface $expectedBlob,
        Bucket $blobBucket,
        Riak $riak
    ) {
        $blob = $blobStorage->uploadBlob($expectedBlob->getData());

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
     * @param BlobManagerInterface $blobStorage
     * @param BlobInterface $expectedBlob
     * @param Bucket $blobBucket
     * @param Riak $riak
     */
    public function testDownload(
        BlobManagerInterface $blobStorage,
        BlobInterface $expectedBlob,
        Bucket $blobBucket,
        Riak $riak
    ) {
        $this->storeObject($expectedBlob->getHash(), $expectedBlob->getData(), $blobBucket, $riak);

        $blob = $blobStorage->downloadBlob($expectedBlob->getHash());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;
        $this->assertContains($blob->getHash(), $blobKeys);
    }
}
