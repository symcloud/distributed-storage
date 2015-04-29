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

use Symcloud\Component\BlobStorage\Model\BlobInterface;

interface BlobManagerInterface
{
    /**
     * @param $data
     *
     * @return BlobInterface
     */
    public function uploadBlob($data);

    /**
     * @param string $hash
     *
     * @return BlobInterface
     */
    public function downloadBlob($hash);
}
