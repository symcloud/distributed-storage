<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;
use Symcloud\Component\BlobStorage\Model\BlobInterface;

interface BlobAdapterInterface
{
    /**
     * @param BlobInterface $blob
     * @return BlobInterface
     */
    public function storeBlob($blob);

    /**
     * @param string $hash
     * @return BlobInterface
     * @throws BlobNotFoundException
     */
    public function fetchBlob($hash);
}
