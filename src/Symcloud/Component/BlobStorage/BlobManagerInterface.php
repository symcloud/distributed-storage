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

use Symcloud\Component\Database\Model\BlobInterface;

interface BlobManagerInterface
{
    /**
     * @param $data
     *
     * @return BlobInterface
     */
    public function upload($data);

    /**
     * @param string $hash
     *
     * @return BlobInterface
     */
    public function download($hash);

    /**
     * @param string $hash
     *
     * @return BlobInterface
     */
    public function downloadProxy($hash);
}
