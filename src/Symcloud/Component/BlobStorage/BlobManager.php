<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;

class BlobManager implements BlobManagerInterface
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
     * BlobManager constructor.
     * @param FactoryInterface $factory
     * @param BlobAdapterInterface $adapter
     */
    public function __construct(FactoryInterface $factory, BlobAdapterInterface $adapter)
    {
        $this->factory = $factory;
        $this->adapter = $adapter;
    }

    /**
     * @param $data
     * @return BlobInterface
     */
    public function uploadBlob($data)
    {
        $blob = $this->factory->createBlob($data);

        if (!$this->adapter->blobExists($blob->getHash())) {
            $this->adapter->storeBlob($blob->getHash(), $blob->getData());
        }

        return $blob;
    }

    /**
     * @param string $hash
     * @return BlobInterface
     */
    public function downloadBlob($hash)
    {
        return $this->factory->createBlob($this->adapter->fetchBlob($hash), $hash);
    }
}
