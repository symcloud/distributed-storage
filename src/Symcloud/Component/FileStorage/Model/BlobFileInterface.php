<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage\Model;

interface BlobFileInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @param int $length
     * @param int $offset
     *
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);
}
