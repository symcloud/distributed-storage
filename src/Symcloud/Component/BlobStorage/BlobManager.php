<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;

class BlobManager
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var BlobAdapterInterface
     */
    private $adapter;

    /**
     * @param $data
     * @return BlobInterface
     */
    public function uploadBlob($data)
    {
        $blob = $this->factory->createBlob($data);

        if (!$this->adapter->blobExists($blob->getHash())) {
            $this->adapter->storeBlob($blob);
        }

        return $blob;
    }

    public function downloadBlob($hash)
    {
        return $this->adapter->fetchBlob($hash);
    }
}
