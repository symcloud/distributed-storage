<?php

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;

interface BlobAdapterInterface
{
    /**
     * @param $hash
     * @param $data
     * @return boolean
     */
    public function storeBlob($hash, $data);

    /**
     * @param string $hash
     * @return string
     * @throws BlobNotFoundException
     */
    public function fetchBlob($hash);

    /**
     * @param string $hash
     * @return boolean
     */
    public function blobExists($hash);
}
