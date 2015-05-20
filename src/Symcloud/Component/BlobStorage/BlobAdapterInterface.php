<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;
use Symcloud\Component\Common\AdapterInterface;

interface BlobAdapterInterface extends AdapterInterface
{
    /**
     * @param string $hash
     * @param string $data
     *
     * @return bool
     */
    public function storeBlob($hash, $data);

    /**
     * @param string $hash
     *
     * @return string
     *
     * @throws BlobNotFoundException
     */
    public function fetchBlob($hash);

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function blobExists($hash);
}
