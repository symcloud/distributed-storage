<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Model\BlobInterface;

interface BlobManagerInterface
{
    /**
     * @param $data
     * @return BlobInterface
     */
    public function uploadBlob($data);

    /**
     * @param string $hash
     * @return BlobInterface
     */
    public function downloadBlob($hash);
}
