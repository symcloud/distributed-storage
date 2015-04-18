<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\AdapterInterface;

class BlobManager
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var AdapterInterface
     */
    private $databaseAdapter;

    /**
     * @param $data
     * @return BlobInterface
     */
    public function uploadBlob($data)
    {
        $blob = $this->factory->createBlob($data);

        return $this->databaseAdapter->saveBlob($blob);
    }

    public function downloadBlob($hash)
    {

    }
}
