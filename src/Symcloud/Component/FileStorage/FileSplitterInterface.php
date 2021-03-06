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

interface FileSplitterInterface
{
    /**
     * @param string   $filePath
     * @param callback $callback
     */
    public function split($filePath, $callback);
}
