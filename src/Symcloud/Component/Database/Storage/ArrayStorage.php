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

    public function store($hash, $object, $context)
    {
        if (!array_key_exists($context, $this->data)) {
            $this->data[$context] = array();
        }
        $this->data[$context][$hash] = $object;

        return true;
    }

    public function fetch($hash, $context)
    {
        if (!$this->contains($hash, $context)) {
            throw new \Exception('Object not found');
        }

        return $this->data[$context][$hash];
    }

    public function contains($hash, $context)
    {
        if (!array_key_exists($context, $this->data)) {
            $this->data[$context] = array();
        }

        return array_key_exists($hash, $this->data[$context]);
    }

    public function delete($hash, $context)
    {
        if (!$this->contains($hash, $context)) {
            return false;
        }

        unset($this->data[$context][$hash]);

        return true;
    }

    public function deleteAll($context = null)
    {
        if ($context) {
            $this->data[$context] = array();
        } else {
            $this->data = array();
        }

        return true;
    }
}
