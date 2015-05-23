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

use Doctrine\Common\Cache\FilesystemCache;

class FilesystemStorage extends FilesystemCache implements StorageAdapterInterface
{
    const EXTENSION = '.symcloud.dat';

    /**
     * FilesystemStorage constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        parent::__construct($directory, self::EXTENSION);
    }

    public function store($hash, $object)
    {
        return parent::save($hash, $object);
    }

    public function fetch($hash)
    {
        if (!$this->contains($hash)) {
            throw new \Exception('Object not found');
        }

        return parent::fetch($hash);
    }

    public function delete($hash)
    {
        return parent::delete($hash);
    }

    public function deleteAll()
    {
        return parent::deleteAll();
    }
}
