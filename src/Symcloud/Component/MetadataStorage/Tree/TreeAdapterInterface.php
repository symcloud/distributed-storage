<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Tree;

interface TreeAdapterInterface
{
    /**
     * @param string $hash
     * @param \JsonSerializable $data
     *
     * @return bool
     */
    public function store($hash, $data);

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetch($hash);
}
