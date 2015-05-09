<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Riak\RiakMetadataAdapter;

trait MetadataAdapterTrait
{
    /**
     * @var RiakMetadataAdapter
     */
    private $serializeAdapter;

    /**
     * @var RiakNamespace
     */
    private $metadataNamespace;

    protected function getSerializeAdapter()
    {
        if (!$this->serializeAdapter) {
            $this->serializeAdapter = new RiakMetadataAdapter($this->getRiak(), $this->getMetadataNamespace());
        }

        return $this->serializeAdapter;
    }

    protected function getMetadataNamespace()
    {
        if (!$this->metadataNamespace) {
            $this->metadataNamespace = new RiakNamespace(RiakNamespace::DEFAULT_TYPE, 'test-metadata');
        }

        return $this->metadataNamespace;
    }

    public abstract function getRiak();
}
