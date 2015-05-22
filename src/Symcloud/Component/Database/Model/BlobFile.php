<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model;

class BlobFile extends Model implements BlobFileInterface
{
    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var BlobInterface[]
     */
    private $blobs;

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

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
    public function setBlobs($blobs)
    {
        $this->blobs = $blobs;
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

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }
}
