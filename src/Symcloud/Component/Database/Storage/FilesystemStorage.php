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
use Symfony\Component\Filesystem\Filesystem;

class FilesystemStorage implements StorageAdapterInterface
{
    const EXTENSION = '.symcloud.dat';

    /**
     * @var FilesystemCache[]
     */
    private $storage = array();

    /**
     * @var string
     */
    private $directory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * FilesystemStorage constructor.
     *
     * @param string $directory
     * @param Filesystem $filesystem
     */
    public function __construct($directory, Filesystem $filesystem)
    {
        $this->directory = $directory;
        $this->filesystem = $filesystem;

        if (!is_dir($directory)) {
            mkdir($directory);
        }
    }

    /**
     * @param string $context
     *
     * @return FilesystemCache
     */
    private function getData($context)
    {
        if (!array_key_exists($context, $this->storage)) {
            $this->storage[$context] = new FilesystemCache($this->directory . '/' . $context, self::EXTENSION);
        }

        return $this->storage[$context];
    }

    public function store($hash, $object, $context)
    {
        $data = $this->getData($context);

        return $data->save($hash, $object);
    }

    public function fetch($hash, $context)
    {
        if (!$this->contains($hash, $context)) {
            throw new \Exception('Object not found');
        }

        $data = $this->getData($context);

        return $data->fetch($hash);
    }

    public function delete($hash, $context)
    {
        $data = $this->getData($context);

        return $data->delete($hash);
    }

    public function deleteAll($context = null)
    {
        $this->filesystem->remove(new \FilesystemIterator($this->directory));

        return true;
    }

    public function contains($hash, $context)
    {
        $data = $this->getData($context);

        return $data->contains($hash);
    }
}
