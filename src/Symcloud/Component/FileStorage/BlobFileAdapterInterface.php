<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage;

use Symcloud\Component\Common\AdapterInterface;

interface BlobFileAdapterInterface extends AdapterInterface
{
    /**
     * @param string   $hash
     * @param array $data
     *
     * @return bool
     */
    public function storeFile($hash, $data);

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function fileExists($hash);

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchFile($hash);
}
