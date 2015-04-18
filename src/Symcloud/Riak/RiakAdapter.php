<?php

namespace Symcloud\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\Database\AdapterInterface;

class RiakAdapter implements AdapterInterface
{
    /**
     * @var Riak
     */
    private $riak;

    /**
     * @var Bucket
     */
    private $blobBucket;

    /**
     * RiakAdapter constructor.
     * @param Riak $riak
     * @param Bucket $blobBucket
     */
    public function __construct(Riak $riak, Bucket $blobBucket)
    {
        $this->riak = $riak;
        $this->blobBucket = $blobBucket;
    }

    /**
     * {@inheritdoc}
     */
    public function saveBlob($blob)
    {
        $response = $this->loadObject($blob->getHash());

        if ($response->isNotFound()) {
            $this->saveObject($blob->getHash(), $blob->getData(), $this->blobBucket);
        }

        return $blob;
    }

    private function loadObject($key)
    {
        return (new Riak\Command\Builder\FetchObject($this->riak))
            ->atLocation(new Riak\Location($key, $this->blobBucket))
            ->build()
            ->execute();
    }

    private function saveObject($key, $data, Bucket $bucket)
    {
        $response = (new Riak\Command\Builder\StoreObject($this->riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();

        return $response;
    }
}
