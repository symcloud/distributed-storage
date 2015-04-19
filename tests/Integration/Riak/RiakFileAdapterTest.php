<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\FileAdapterInterface;
use Symcloud\Riak\RiakFileAdapter;

class RiakFileAdapterTest extends ProphecyTestCase
{
    public function adapterProvider()
    {
        $factory = new Factory('md5', 'ThisIsMySecretValue');

        $riak = $this->getRiak();
        $fileBucket = $this->getFileBucket();
        $adapter = new RiakFileAdapter($riak, $fileBucket);

        return array(
            array($adapter, $riak, $fileBucket, $factory)
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFile(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));

        $response = $this->fetchBucketKeys($fileBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($file->getHash(), $response->getObject()->getData()->keys[0]);

        $response = $this->fetchObject($file->getHash(), $fileBucket, $riak);
        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($file->getBlobs(), $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testStoreFileAlreadyExists(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        // no exception expected
        $this->assertTrue($adapter->storeFile($file->getHash(), $file->getBlobs()));
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     *
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFile(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));
        $adapter->fetchFile($file->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFetchFileNotExists(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        $result = $adapter->fetchFile($file->getHash());

        $this->assertEquals($file->getBlobs(), $result);
    }

    /**
     * @dataProvider adapterProvider
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFileExists(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));
        $this->storeObject($file->getHash(), $file->getBlobs(), $fileBucket, $riak);

        $this->assertTrue($adapter->fileExists($file->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     * @param FileAdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $fileBucket
     * @param FactoryInterface $factory
     */
    public function testFileNotExists(
        FileAdapterInterface $adapter,
        Riak $riak,
        Bucket $fileBucket,
        FactoryInterface $factory
    ) {
        $this->clearBucket($fileBucket, $riak);

        $file = $factory->createFile('my-hash', array('hash1', 'hash2'));

        $this->assertFalse($adapter->fileExists($file->getHash()));
    }

    private function getRiak()
    {
        $nodes = (new Builder())
            ->buildLocalhost([8098]);

        return new Riak($nodes);
    }

    private function getFileBucket()
    {
        return new Riak\Bucket('test-files');
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
