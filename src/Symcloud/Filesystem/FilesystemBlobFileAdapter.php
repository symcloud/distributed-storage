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

use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;

class FilesystemBlobFileAdapter extends FilesystemBaseAdapter implements BlobFileAdapterInterface
{
    const FILE_EXTENSION = '.symcloud.blob-file.json';

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
    public function storeFile($hash, $data)
    {
        return $this->save($hash, json_encode($data));
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($hash)
    {
        return $this->contains($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        if (!$this->fileExists($hash)) {
            throw new FileNotFoundException($hash);
        }

        return json_decode($this->fetch($hash), true);
    }
}
