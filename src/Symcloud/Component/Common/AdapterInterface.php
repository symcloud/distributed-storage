<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Common;

interface AdapterInterface
{
    /**
     * @param string $hash
     *
     * @return bool
     */
    public function remove($hash);

    /**
     * @return string[]
     */
    public function fetchHashes();
}
