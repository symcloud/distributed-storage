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

class ArrayStorage implements StorageAdapterInterface
{
    private $data = array();

    public function store($hash, $object)
    {
        $this->data[$hash] = $object;
    }

    public function fetch($hash)
    {
        return $this->data[$hash];
    }

    public function contains($hash)
    {
        return array_key_exists($hash, $this->data);
    }

    public function delete($hash)
    {
        unset($this->data[$hash]);
    }

    public function deleteAll()
    {
        $this->data = array();
    }
}
