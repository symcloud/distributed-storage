<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Filesystem;

use Doctrine\Common\Cache\FilesystemCache;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;

class FilesystemBlobAdapter extends FilesystemCache implements BlobAdapterInterface
{
    const FILE_EXTENSION = '.symcloud.blob.dat';

    /**
     * FilesystemBlobAdapter constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        parent::__construct($directory, self::FILE_EXTENSION);
    }

    /**
     * @param string $hash
     * @param string $data
     *
     * @return bool
     */
    public function storeBlob($hash, $data)
    {
        return $this->save($hash, $data);
    }

    /**
     * @param string $hash
     *
     * @return string
     *
     * @throws BlobNotFoundException
     */
    public function fetchBlob($hash)
    {
        return $this->fetch($hash);
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function blobExists($hash)
    {
        return $this->contains($hash);
    }
}
