<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage\Model;

use Symcloud\Component\BlobStorage\Model\BlobInterface;

class BlobFileModel implements BlobFileInterface
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
     * @return BlobInterface[]
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
        if ($length !== -1 || $offset !== 0) {
            throw new \Exception('Not implemented');
        }

        $content = '';
        foreach ($this->getBlobs() as $blob) {
            $content .= $blob->getData();
        }

        return $content;
    }
}
