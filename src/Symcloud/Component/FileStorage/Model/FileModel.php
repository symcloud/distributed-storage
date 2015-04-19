<?php

namespace Symcloud\Component\FileStorage\Model;

use Symcloud\Component\BlobStorage\Model\BlobInterface;

class FileModel implements FileInterface
{
    /**
     * @var BlobInterface[]
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
     * @param BlobInterface[] $blobs
     */
    public function setBlobs(array $blobs)
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

    /**
     * {@inheritdoc}
     */
    public function getContent($length = -1, $offset = 0)
    {
        throw new \Exception('Not implemented');
    }
}
