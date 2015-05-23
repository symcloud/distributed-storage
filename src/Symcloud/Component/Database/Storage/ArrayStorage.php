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

        return true;
    }

    public function fetch($hash)
    {
        if (!$this->contains($hash)) {
            throw new \Exception('Object not found');
        }

        return $this->data[$hash];
    }

    public function contains($hash)
    {
        return array_key_exists($hash, $this->data);
    }

    public function delete($hash)
    {
        if (!array_key_exists($hash, $this->data)) {
            return false;
        }

        unset($this->data[$hash]);

        return true;
    }

    public function deleteAll()
    {
        $this->data = array();

        return true;
    }
}
