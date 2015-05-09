<?php

namespace Integration\Riak;

use Integration\Parts\BlobManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\Common\FactoryInterface;

class RiakBlobAdapterTest extends ProphecyTestCase
{
    use BlobManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobNamespace());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getBlobAdapter(), $this->getBlobNamespace(), $this->getFactory())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreBlob(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $blob = $factory->createBlob('This is my data');
        $adapter->storeBlob($blob->getHash(), $blob->getData());

        $response = $this->fetchObject($blob->getHash(), $blobNamespace);
        $this->assertFalse($response->getNotFound());
        $this->assertEquals($blob->getData(), $response->getValue()->getValue()->getContents());

        $keys = $this->fetchBucketKeys($blobNamespace);
        $this->assertContains($blob->getHash(), $keys);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreBlobAlreadyExists(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobNamespace);

        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobNamespace);

        // no exception expected
        $adapter->storeBlob($blob->getHash(), $blob->getData());
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\BlobStorage\Exception\BlobNotFoundException
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchBlobNotExists(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $blob = $factory->createBlob('This is my not existing data');
        $adapter->fetchBlob($blob->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchBlob(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobNamespace);

        $result = $adapter->fetchBlob($blob->getHash());

        $this->assertEquals($blob->getData(), $result);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testBlobExists(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobNamespace);

        $blob = $factory->createBlob('This is my data');
        $this->storeObject($blob->getHash(), $blob->getData(), $blobNamespace);

        $this->assertTrue($adapter->blobExists($blob->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobAdapterInterface $adapter
     * @param RiakNamespace $blobNamespace
     * @param FactoryInterface $factory
     */
    public function testBlobNotExists(
        BlobAdapterInterface $adapter,
        RiakNamespace $blobNamespace,
        FactoryInterface $factory
    ) {
        $this->clearBucket($blobNamespace);

        $blob = $factory->createBlob('This is my not existing data');

        $this->assertFalse($adapter->blobExists($blob->getHash()));
    }
}
