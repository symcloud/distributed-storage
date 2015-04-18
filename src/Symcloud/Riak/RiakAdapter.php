<?php

namespace Symcloud\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;
use Symcloud\Component\Common\FactoryInterface;

class RiakAdapter implements BlobAdapterInterface
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
    public function storeBlob($hash, $data)
    {
        return $this->storeObject($hash, $data, $this->blobBucket)->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    public function blobExists($hash)
    {
        $response = $this->fetchObject($hash);

        return $response->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchBlob($hash)
    {
        $response = $this->fetchObject($hash);

        if ($response->isNotFound()) {
            throw new BlobNotFoundException($hash);
        }

        return $response->getObject()->getData();
    }

    private function fetchObject($key)
    {
        return (new Riak\Command\Builder\FetchObject($this->riak))
            ->atLocation(new Riak\Location($key, $this->blobBucket))
            ->build()
            ->execute();
    }

    private function storeObject($key, $data, Bucket $bucket)
    {
        $response = (new Riak\Command\Builder\StoreObject($this->riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();

        return $response;
    }
}
