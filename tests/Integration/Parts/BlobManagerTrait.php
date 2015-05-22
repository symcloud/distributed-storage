<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Riak\RiakBlobAdapter;

trait BlobManagerTrait
{
    use DatabaseTrait;

    /**
     * @var BlobManagerInterface
     */
    private $blobManager;

    protected function getBlobManager()
    {
        if (!$this->blobManager) {
            $this->blobManager = new BlobManager($this->getFactory(), $this->getDatabase());
        }

        return $this->blobManager;
    }
}
