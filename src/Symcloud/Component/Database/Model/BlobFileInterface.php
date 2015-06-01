<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model;

interface BlobFileInterface
{
    public function getHash();

    public function getBlobs();

    public function getSize();

    public function getMimetype();

    public function getContent($length = -1, $offset = 0);
}
