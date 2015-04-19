<?php

namespace Symcloud\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;
use Symcloud\Component\FileStorage\FileAdapterInterface;

class RiakFileAdapter extends RiakBaseAdapter implements FileAdapterInterface
{
    /**
     * @var Bucket
     */
    private $fileBucket;

    /**
     * RiakFileAdapter constructor.
     * @param Riak $riak
     * @param Bucket $fileBucket
     */
    public function __construct(Riak $riak, Bucket $fileBucket)
    {
        parent::__construct($riak);

        $this->fileBucket = $fileBucket;
    }

    /**
     * {@inheritdoc}
     */
    public function storeFile($hash, $blobs)
    {
        return $this->storeObject($hash, $blobs, $this->fileBucket)->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($hash)
    {
        $response = $this->fetchObject($hash, $this->fileBucket);

        return $response->isSuccess();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        $response = $this->fetchObject($hash, $this->fileBucket);

        if ($response->isNotFound()) {
            throw new FileNotFoundException($hash);
        }

        return $response->getObject()->getData();
    }
}
