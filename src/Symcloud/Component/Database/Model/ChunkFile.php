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

class ChunkFile implements ChunkFileInterface
{
    private $hash;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var ChunkInterface[]
     */
    private $chunks;

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
     * @return ChunkInterface[]
     */
    public function getChunks()
    {
        return $this->chunks;
    }

    /**
     * @param ChunkInterface[] $chunks
     */
    public function setChunks($chunks)
    {
        $this->chunks = $chunks;
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
        foreach ($this->getChunks() as $chunk) {
            $content .= $chunk->getData();
        }

        return $content;
    }
}
