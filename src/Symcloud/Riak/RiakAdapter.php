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
     * @var FactoryInterface
     */
    private $factory;

    /**
     * RiakAdapter constructor.
     * @param Riak $riak
     * @param Bucket $blobBucket
     * @param FactoryInterface $factory
     */
    public function __construct(Riak $riak, Bucket $blobBucket, FactoryInterface $factory)
    {
        $this->riak = $riak;
        $this->blobBucket = $blobBucket;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function storeBlob($blob)
    {
        $response = $this->fetchObject($blob->getHash());

        if ($response->isNotFound()) {
            $this->saveObject($blob->getHash(), $blob->getData(), $this->blobBucket);
        }

        return $blob;
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

        return $this->factory->createBlob($response->getObject()->getData(), $hash);
    }

    private function fetchObject($key)
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
