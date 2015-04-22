<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Integration\BaseIntegrationTest;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Riak\RiakBlobAdapter;

class RiakBlobAdapterTest extends BaseIntegrationTest
{
    public function adapterProvider()
    {
        $factory = $this->getFactory();

        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();
        $adapter = new RiakBlobAdapter($riak, $blobBucket);

        return array(
            array($adapter, $riak, $blobBucket, $factory)
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testStoreBlob(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');
        $this->assertTrue($adapter->storeBlob($blob->getHash(), $blob->getData()));

        $response = $this->fetchBucketKeys($blobBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertContains($blob->getHash(), $response->getObject()->getData()->keys);

        $response = $this->fetchObject($blob->getHash(), $blobBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($blob->getData(), $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testStoreBlobAlreadyExists(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobBucket, $riak);

        // no exception expected
        $this->assertTrue($adapter->storeBlob($blob->getHash(), $blob->getData()));
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\BlobStorage\Exception\BlobNotFoundException
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testFetchBlob(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');
        $adapter->fetchBlob($blob->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testFetchBlobNotExists(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobBucket, $riak);

        $result = $adapter->fetchBlob($blob->getHash());

        $this->assertEquals($blob->getData(), $result);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testBlobExists(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobBucket, $riak);

        $this->assertTrue($adapter->blobExists($blob->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testBlobNotExists(
        BlobAdapterInterface $adapter,
        Riak $riak,
        Bucket $blobBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobBucket, $riak);

        $blob = $factory->createBlob('This is my data');

        $this->assertFalse($adapter->blobExists($blob->getHash()));
    }
}