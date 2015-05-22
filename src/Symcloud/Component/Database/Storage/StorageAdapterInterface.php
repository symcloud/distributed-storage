<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Storage;

interface StorageAdapterInterface
{
    public function store($hash, $object);

    public function fetch($hash);

    public function delete($hash);

    public function deleteAll();
}
