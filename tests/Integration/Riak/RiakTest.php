<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Riak\RiakAdapter;

class RiakTest extends ProphecyTestCase
{
    public function adapterProvider()
    {
        $factory = new Factory('md5', 'ThisIsMySecretValue');

        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();
        $adapter = new RiakAdapter($riak, $blobBucket, $factory);

        return array(
            array($adapter, $riak, $blobBucket, $factory)
        );
    }

    /**
     * @dataProvider adapterProvider
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
        $adapter->storeBlob($blob);

        $response = $this->fetchBucketKeys($blobBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($blob->getHash(), $response->getObject()->getData()->keys[0]);

        $response = $this->fetchObject($blob->getHash(), $blobBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($blob->getData(), $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
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
        $this->storeObject($blob->getHash(), $blob->getData(), $blobBucket, $riak);

        $result = $adapter->fetchBlob($blob->getHash());

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    /**
     * @dataProvider adapterProvider
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

    private function getRiak()
    {
        $nodes = (new Builder())
            ->buildLocalhost([8098]);

        return new Riak($nodes);
    }

    private function getBlobBucket()
    {
        return new Riak\Bucket('test-blobs');
    }

    private function clearBucket(Bucket $bucket, Riak $riak)
    {
        $response = $this->fetchBucketKeys($bucket, $riak);

        foreach ($response->getObject()->getData()->keys as $key) {
            $this->deleteObject($key, $bucket, $riak);
        }
    }

    private function fetchBucketKeys(Bucket $bucket, Riak $riak)
    {
        $fetchObject = (new Riak\Command\Builder\FetchObject($riak))
            ->inBucket($bucket);

        return (new Riak\Command\Bucket\Keys($fetchObject))
            ->execute();
    }

    private function fetchObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\FetchObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    private function storeObject($key, $data, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\StoreObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();
    }

    private function deleteObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\DeleteObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }
}
