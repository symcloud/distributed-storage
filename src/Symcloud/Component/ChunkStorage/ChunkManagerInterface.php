<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\ChunkStorage;

use Symcloud\Component\Database\Model\ChunkInterface;

interface ChunkManagerInterface
{
    /**
     * @param $data
     *
     * @return ChunkInterface
     */
    public function upload($data);

    /**
     * @param string $hash
     *
     * @return ChunkInterface
     */
    public function download($hash);

    /**
     * @param string $hash
     *
     * @return ChunkInterface
     */
    public function downloadProxy($hash);
}
