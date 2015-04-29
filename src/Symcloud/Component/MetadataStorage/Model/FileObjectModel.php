<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

class FileObjectModel extends ObjectModel implements FileObjectInterface
{
    /**
     * @var string
     */
    private $fileHash;

    /**
     * {@inheritdoc}
     */
    public function getFileHash()
    {
        return $this->fileHash;
    }

    /**
     * @param string $fileHash
     */
    public function setFileHash($fileHash)
    {
        $this->fileHash = $fileHash;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return true;
    }
}
