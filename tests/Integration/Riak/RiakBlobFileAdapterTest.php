<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Integration\Parts\BlobFileManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;

class RiakBlobFileAdapterTest extends ProphecyTestCase
{
    use BlobFileManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobFileBucket());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getBlobFileAdapter(), $this->getBlobFileBucket(), $this->getFactory())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFile(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));

        $response = $this->fetchBucketKeys($blobFileBucket);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertContains($file->getHash(), $response->getObject()->getData()->keys);

        $response = $this->fetchObject($file->getHash(), $blobFileBucket);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($file->getBlobs(), $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFileAlreadyExists(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileBucket);

        // no exception expected
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFile(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $adapter->fetchFile($file->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFileNotExists(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileBucket);

        $result = $adapter->fetchFile($file->getHash());

        $this->assertEquals($file->getBlobs(), $result);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testFileExists(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $blobFileBucket);

        $this->assertTrue($adapter->fileExists($file->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Bucket $blobFileBucket
     * @param FactoryInterface $factory
     */
    public function testFileNotExists(
        BlobFileAdapterInterface $adapter,
        Bucket $blobFileBucket,
        FactoryInterface $factory
    ) {
        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));

        $this->assertFalse($adapter->fileExists($file->getHash()));
    }
}
