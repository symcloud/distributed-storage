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
    private $databaseAdapter;

    /**
     * @param $data
     * @return BlobInterface
     */
    public function uploadBlob($data)
    {
        $blob = $this->factory->createBlob($data);

        return $this->databaseAdapter->storeBlob($blob);
    }

    public function downloadBlob($hash)
    {

    }
}
