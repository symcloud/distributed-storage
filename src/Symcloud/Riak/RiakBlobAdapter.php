<?php

namespace Symcloud\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;

class RiakBlobAdapter extends RiakBaseAdapter implements BlobAdapterInterface
{

    /**
     * @var Bucket
     */
    private $blobBucket;

    /**
     * RiakBlobAdapter constructor.
     * @param Riak $riak
     * @param Bucket $blobBucket
     */
    public function __construct(Riak $riak, Bucket $blobBucket)
    {
        parent::__construct($riak);

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
        $response = $this->fetchObject($hash, $this->blobBucket);

        return $response->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchBlob($hash)
    {
        $response = $this->fetchObject($hash, $this->blobBucket);

        if ($response->isNotFound()) {
            throw new BlobNotFoundException($hash);
        }

        return $response->getObject()->getData();
    }
}
