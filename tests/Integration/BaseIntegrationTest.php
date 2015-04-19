<?php

namespace Integration;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\Factory;

abstract class BaseIntegrationTest extends ProphecyTestCase
{
    protected function getFactory()
    {
        return new Factory('md5', 'ThisIsMySecretValue');
    }

    protected function getRiak()
    {
        $nodes = (new Builder())
            ->buildLocalhost([8098]);

        return new Riak($nodes);
    }

    protected function getBlobBucket()
    {
        return new Riak\Bucket('test-blobs');
    }

    protected function getFileBucket()
    {
        return new Riak\Bucket('test-files');
    }

    protected function clearBucket(Bucket $bucket, Riak $riak)
    {
        $response = $this->fetchBucketKeys($bucket, $riak);

        foreach ($response->getObject()->getData()->keys as $key) {
            $this->deleteObject($key, $bucket, $riak);
        }
    }

    protected function fetchBucketKeys(Bucket $bucket, Riak $riak)
    {
        $fetchObject = (new Riak\Command\Builder\FetchObject($riak))
            ->inBucket($bucket);

        return (new Riak\Command\Bucket\Keys($fetchObject))
            ->execute();
    }

    protected function fetchObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\FetchObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    protected function storeObject($key, $data, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\StoreObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();
    }

    protected function deleteObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\DeleteObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }
}
