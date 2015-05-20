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

use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;

class FilesystemBlobAdapter extends FilesystemBaseAdapter implements BlobAdapterInterface
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
     * {@inheritdoc}
     */
    public function storeBlob($hash, $data)
    {
        return $this->save($hash, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchBlob($hash)
    {
        if (!$this->blobExists($hash)) {
            throw new BlobNotFoundException($hash);
        }

        return $this->fetch($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function blobExists($hash)
    {
        return $this->contains($hash);
    }
}
