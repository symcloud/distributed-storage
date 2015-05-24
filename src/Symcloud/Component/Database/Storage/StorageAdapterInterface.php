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
    public function store($hash, $object, $context);

    public function fetch($hash, $context);

    public function contains($hash, $context);

    public function delete($hash, $context);

    public function deleteAll($context = null);
}
