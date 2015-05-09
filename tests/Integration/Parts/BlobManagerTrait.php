<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
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
     * @var RiakNamespace
     */
    private $blobNamespace;

    protected function getBlobAdapter()
    {
        if (!$this->blobAdapter) {
            $this->blobAdapter = new RiakBlobAdapter($this->getRiak(), $this->getBlobNamespace());
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

    protected function getBlobNamespace()
    {
        if (!$this->blobNamespace) {
            $this->blobNamespace = new RiakNamespace(RiakNamespace::DEFAULT_TYPE, 'test-blobs');
        }

        return $this->blobNamespace;
    }
}
