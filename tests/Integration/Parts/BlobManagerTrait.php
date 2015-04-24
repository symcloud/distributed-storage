<?php

namespace Integration\Parts;

use Basho\Riak\Bucket;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Riak\RiakBlobAdapter;

trait BlobManagerTrait
{
    use FactoryTrait, RiakTrait;

    /**
     * @var BlobManagerInterface
     */
    private $blobManager;

    /**
     * @var BlobAdapterInterface
     */
    private $blobAdapter;

    /**
     * @var Bucket
     */
    private $blobBucket;

    protected function getBlobAdapter()
    {
        if (!$this->blobAdapter) {
            $this->blobAdapter = new RiakBlobAdapter($this->getRiak(), $this->getBlobBucket());
        }

        return $this->blobAdapter;
    }

    protected function getBlobManager()
    {
        if (!$this->blobManager) {
            $this->blobManager = new BlobManager($this->getFactory(), $this->getBlobAdapter());
        }

        return $this->blobManager;
    }

    protected function getBlobBucket()
    {
        if (!$this->blobBucket) {
            $this->blobBucket = new Bucket('test-blobs');
        }

        return $this->blobBucket;
    }
}
