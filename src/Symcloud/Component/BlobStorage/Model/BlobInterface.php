<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\BlobStorage\Model;

interface BlobInterface
{
    /**
     * @return string
     */
    public function getData();

    /**
     * @return string
     */
    public function getHash();
}
