<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;

interface BlobFileManagerInterface
{
    /**
     * @param string $filePath
     *
     * @return BlobFileInterface
     */
    public function upload($filePath);

    /**
     * @param string $fileHash
     *
     * @return BlobFileInterface
     */
    public function download($fileHash);

    /**
     * @param FileObjectInterface $object
     *
     * @return BlobFileInterface
     */
    public function downloadByObject(FileObjectInterface $object);
}
