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
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;

class FilesystemBlobFileAdapter extends FilesystemCache implements BlobFileAdapterInterface
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
     * @param string $hash
     * @param array $data
     *
     * @return bool
     */
    public function storeFile($hash, $data)
    {
        return $this->save($hash, json_encode($data));
    }

    /**
     * @param string $hash
     *
     * @return bool
     */
    public function fileExists($hash)
    {
        return $this->contains($hash);
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchFile($hash)
    {
        return json_decode($this->fetch($hash));
    }
}
