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

use Symcloud\Component\Database\Model\ChunkFileInterface;
use Symcloud\Component\Database\Model\ChunkInterface;

interface ChunkFileManagerInterface
{
    /**
     * @param string $filePath
     * @param $mimeType
     * @param $size
     *
     * @return ChunkFileInterface
     */
    public function upload($filePath, $mimeType, $size);

    /**
     * @param string $hash
     * @param ChunkInterface[] $chunks
     * @param string $mimetype
     * @param int $size
     *
     * @return ChunkFileInterface
     */
    public function download($hash, array $chunks, $mimetype, $size);
}
