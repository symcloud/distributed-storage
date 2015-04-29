<?php

namespace Integration\Parts;

use Basho\Riak\Bucket;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Riak\RiakBlobAdapter;
use Symcloud\Riak\RiakSerializeAdapter;

trait SerializeAdapterTrait
{
    /**
     * @var RiakSerializeAdapter
     */
    private $serializeAdapter;

    /**
     * @var Bucket
     */
    private $metadataBucket;

    protected function getSerializeAdapter()
    {
        if (!$this->serializeAdapter) {
            $this->serializeAdapter = new RiakSerializeAdapter($this->getRiak(), $this->getMetadataBucket());
        }

        return $this->serializeAdapter;
    }

    protected function getMetadataBucket()
    {
        if (!$this->metadataBucket) {
            $this->metadataBucket = new Bucket('test-metadata');
        }

        return $this->metadataBucket;
    }

    public abstract function getRiak();
}