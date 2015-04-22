<?php

namespace Integration\Component\BlobStorage;

use Basho\Riak;
use Basho\Riak\Bucket;
use Integration\BaseIntegrationTest;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Riak\RiakBlobAdapter;

class BlobStorageTest extends BaseIntegrationTest
{
    public function storageProvider()
    {
        $factory = $this->getFactory();
        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();

        $blobAdapter = new RiakBlobAdapter($riak, $blobBucket);
        $blobStorage = new BlobManager($factory, $blobAdapter);

        $length = 200;
        $data = $this->generateString($length);

        return array(
            array($blobStorage, $data, $blobBucket, $riak, $factory)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobStorage
     * @param $data
     * @param Bucket $blobBucket
     * @param Riak $riak
     * @param FactoryInterface $factory
     */
    public function testUpload(
        BlobManagerInterface $blobStorage,
        $data,
        Bucket $blobBucket,
        Riak $riak,
        FactoryInterface $factory
    ) {
        $expectedBlob = $factory->createBlob($data);

        $blob = $blobStorage->uploadBlob($data);

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $riakResponse = $this->fetchObject($blob->getHash(), $blobBucket, $riak);
        $this->assertEquals($data, $riakResponse->getObject()->getData());

        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;
        $this->assertContains($blob->getHash(), $blobKeys);
    }

    public function testDownload()
    {
        // TODO download test
    }
}
