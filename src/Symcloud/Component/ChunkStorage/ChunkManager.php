<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\ChunkStorage;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\Database\Replication\ReplicatorInterface;

class ChunkManager implements ChunkManagerInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * ChunkManager constructor.
     *
     * @param FactoryInterface $factory
     * @param DatabaseInterface $database
     */
    public function __construct(FactoryInterface $factory, DatabaseInterface $database)
    {
        $this->factory = $factory;
        $this->database = $database;
    }

    /**
     * @param $data
     *
     * @return ChunkInterface
     */
    public function upload($data)
    {
        $chunk = new Chunk();
        $chunk->setData($data);
        $chunk->setLength(strlen($data));
        $chunk->setPolicyCollection(new PolicyCollection());
        $chunk->setHash($this->factory->createHash($data));

        if ($this->database->contains($chunk->getHash(), Chunk::class)) {
            return $chunk;
        }

        return $this->database->store($chunk, array(ReplicatorInterface::OPTION_NAME => ReplicatorInterface::TYPE_FULL));
    }

    /**
     * @param string $hash
     *
     * @return ChunkInterface
     */
    public function download($hash)
    {
        return $this->database->fetch($hash, Chunk::class);
    }

    public function downloadProxy($hash)
    {
        return $this->factory->createProxy(
            ChunkInterface::class,
            function () use ($hash) {
                return $this->download($hash);
            }
        );
    }
}
