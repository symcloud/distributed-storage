<?php

namespace Symcloud\Component\Common;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\BlobStorage\Model\BlobModel;

interface FactoryInterface
{

    /**
     * @param string $data
     * @param string|null $hash
     * @return BlobInterface
     */
    public function createBlob($data, $hash = null);

    /**
     * @param $data
     * @return string
     */
    public function createHash($data);
}
