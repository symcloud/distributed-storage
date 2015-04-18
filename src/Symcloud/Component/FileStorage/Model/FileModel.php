<?php

namespace Symcloud\Component\FileStorage\Model;

use Symcloud\Component\BlobStorage\Model\BlobInterface;

class FileModel implements FileInterface
{
    /**
     * @var BlobInterface
     */
    private $blobs;

    /**
     * @var string
     */
    private $hash;

    /**
     * @return mixed
     */
    public function getBlobs()
    {
        return $this->blobs;
    }

    /**
     * @param mixed $blobs
     */
    public function setBlobs($blobs)
    {
        $this->blobs = $blobs;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
}
