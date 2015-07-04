<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\ChunkStorage\ChunkManager;
use Symcloud\Component\ChunkStorage\ChunkManagerInterface;

trait ChunkManagerTrait
{
    use DatabaseTrait;

    /**
     * @var ChunkManagerInterface
     */
    private $chunkManager;

    protected function getChunkManager()
    {
        if (!$this->chunkManager) {
            $this->chunkManager = new ChunkManager($this->getFactory(), $this->getDatabase());
        }

        return $this->chunkManager;
    }
}
