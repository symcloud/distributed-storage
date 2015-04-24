<?php

namespace Integration\Parts;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;

trait RiakTrait
{
    /**
     * @var Riak
     */
    private $riak;

    protected function getRiak()
    {
        if (!$this->riak) {
            $nodes = (new Builder())
                ->buildLocalhost([8098]);

            $this->riak = new Riak($nodes);
        }

        return $this->riak;
    }

    protected function clearBucket(Bucket $bucket)
    {
        $response = $this->fetchBucketKeys($bucket);

        foreach ($response->getObject()->getData()->keys as $key) {
            $this->deleteObject($key, $bucket);
        }
    }

    protected function fetchBucketKeys(Bucket $bucket)
    {
        $fetchObject = (new Riak\Command\Builder\FetchObject($this->getRiak()))
            ->inBucket($bucket);

        return (new Riak\Command\Bucket\Keys($fetchObject))
            ->execute();
    }

    protected function fetchObject($key, Bucket $bucket)
    {
        return (new Riak\Command\Builder\FetchObject($this->getRiak()))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    protected function storeObject($key, $data, Bucket $bucket)
    {
        return (new Riak\Command\Builder\StoreObject($this->getRiak()))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();
    }

    protected function deleteObject($key, Bucket $bucket)
    {
        return (new Riak\Command\Builder\DeleteObject($this->getRiak()))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }
}
