<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Integration\BaseIntegrationTest;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Riak\RiakBlobFileAdapter;

class RiakFileAdapterTest extends BaseIntegrationTest
{
    public function adapterProvider()
    {
        $factory = new Factory('md5', 'ThisIsMySecretValue');

        $riak = $this->getRiak();
        $fileBucket = $this->getBlobFileBucket();
        $adapter = new RiakBlobFileAdapter($riak, $fileBucket);

        return array(
            array($adapter, $riak, $fileBucket, $factory)
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFile(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));

        $response = $this->fetchBucketKeys($fileBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertContains($file->getHash(), $response->getObject()->getData()->keys);

        $response = $this->fetchObject($file->getHash(), $fileBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($file->getBlobs(), $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFileAlreadyExists(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        // no exception expected
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFile(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $adapter->fetchFile($file->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFileNotExists(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        $result = $adapter->fetchFile($file->getHash());

        $this->assertEquals($file->getBlobs(), $result);
    }

    /**
     * @dataProvider adapterProvider
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFileExists(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        $this->assertTrue($adapter->fileExists($file->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     * @param BlobFileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFileNotExists(
        BlobFileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createBlobFile('my-hash', array('hash1', 'hash2'));

        $this->assertFalse($adapter->fileExists($file->getHash()));
    }
}
