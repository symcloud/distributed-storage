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

interface BlobFileAdapterInterface
{
    /**
     * @param string   $hash
     * @param string[] $blobs
     *
     * @return bool
     */
    public function storeFile($hash, $blobs);

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function fileExists($hash);

    /**
     * @param string $hash
     *
     * @return string[]
     */
    public function fetchFile($hash);
}
