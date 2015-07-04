<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\FileStorage\ChunkFileManager;
use Symcloud\Component\FileStorage\ChunkFileManagerInterface;
use Symcloud\Component\FileStorage\FileSplitter;
use Symcloud\Component\FileStorage\FileSplitterInterface;

trait ChunkFileManagerTrait
{
    use ChunkManagerTrait;

    /**
     * @var ChunkFileManagerInterface
     */
    private $chunkFileManager;

    /**
     * @var FileSplitterInterface
     */
    private $fileSplitter;

    protected function getChunkMaxLength()
    {
        return 100;
    }

    protected function getFileSplitter()
    {
        if (!$this->fileSplitter) {
            $this->fileSplitter = new FileSplitter($this->getChunkMaxLength());
        }

        return $this->fileSplitter;
    }

    protected function getChunkFileManager()
    {
        if (!$this->chunkFileManager) {
            $this->chunkFileManager = new ChunkFileManager(
                $this->getFileSplitter(),
                $this->getChunkManager(),
                $this->getFactory()
            );
        }

        return $this->chunkFileManager;
    }
}
