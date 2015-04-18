<?php

namespace Integration\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\AdapterInterface;
use Symcloud\Riak\RiakAdapter;

class RiakTest extends ProphecyTestCase
{
    public function adapterProvider()
    {
        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();
        $adapter = new RiakAdapter($riak, $blobBucket);
        $factory = new Factory('md5', 'ThisIsMySecretValue');

        return array(
            array($adapter, $riak, $blobBucket, $factory)
        );
    }

    /**
     * @dataProvider adapterProvider
     * @param AdapterInterface $adapter
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function testSaveBlob(AdapterInterface $adapter, Riak $riak, Bucket $blobBucket, FactoryInterface $factory)
    {

        $this->assertTrue($this->clearBucket($blobBucket, $riak));

        $blob = $factory->createBlob('This are my data');

        $adapter->saveBlob($blob);

        $response = $this->fetchBucketKeys($blobBucket, $riak);

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($blob->getHash(), $response->getObject()->getData()->keys[0]);

        $response = (new Riak\Command\Builder\FetchObject($riak))
            ->atLocation(new Riak\Location($blob->getHash(), $blobBucket))
            ->build()
            ->execute();

        $this->assertTrue($response->isSuccess());
        $this->assertFalse($response->isNotFound());
        $this->assertEquals($blob->getData(), $response->getObject()->getData());
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
        $deleteObject = (new Riak\Command\Builder\DeleteObject($riak))
            ->atLocation(new Riak\Location('...', $bucket));

        $response = (new Riak\Command\Bucket\Delete($deleteObject))
            ->execute();

        return $response->isSuccess();
    }

    private function fetchBucketKeys(Bucket $bucket, Riak $riak)
    {
        $fetchObject = (new Riak\Command\Builder\FetchObject($riak))
            ->inBucket($bucket);

        return (new Riak\Command\Bucket\Keys($fetchObject))
            ->execute();
    }
}
