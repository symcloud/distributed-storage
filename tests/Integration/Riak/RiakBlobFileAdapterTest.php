<?php

namespace Integration\Riak;

use Integration\Parts\BlobFileManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;

class RiakBlobFileAdapterTest extends ProphecyTestCase
{
    use BlobFileManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobFileNamespace());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getBlobFileAdapter(), $this->getBlobFileNamespace(), $this->getFactory())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreFile(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $adapter->storeFile($file->getHash(), $file->getBlobs());

        $response = $this->fetchObject($file->getHash(), $blobFileNamespace);
        $this->assertFalse($response->getNotFound());
        $this->assertEquals($file->getBlobs(), json_decode($response->getValue()->getValue()));

        $keys = $this->fetchBucketKeys($blobFileNamespace);
        $this->assertContains($file->getHash(), $keys);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreFileAlreadyExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileNamespace);

        // no exception expected
        $adapter->storeFile($file->getHash(), $file->getBlobs());
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchFileNotExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-not-existing-hash', array('hash1', 'hash2'));
        $adapter->fetchFile($file->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchFile(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileNamespace);

        $result = $adapter->fetchFile($file->getHash());

        $this->assertEquals($file->getBlobs(), $result);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFileExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileNamespace);

        $this->assertTrue($adapter->fileExists($file->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFileNotExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-not-existing-hash', array('hash1', 'hash2'));

        $this->assertFalse($adapter->fileExists($file->getHash()));
    }
}
