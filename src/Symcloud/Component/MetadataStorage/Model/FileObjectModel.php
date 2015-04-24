<?php

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
     * @return boolean
     */
    public function isFile()
    {
        return true;
    }
}
