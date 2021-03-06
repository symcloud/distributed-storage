<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model\Tree;

use Symcloud\Component\Database\Model\ChunkFileInterface;
use Symcloud\Component\Database\Model\ChunkInterface;

interface TreeFileInterface extends TreeNodeInterface
{
    /**
     * @return string
     */
    public function getFileHash();

    /**
     * @param ChunkFileInterface $file
     */
    public function setFile(ChunkFileInterface $file);

    /**
     * @return ChunkInterface[]
     */
    public function getChunks();

    /**
     * @return int
     */
    public function getSize();

    /**
     * @return string
     */
    public function getMimetype();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasMetadata($name);

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getMetadataProperty($name);

    /**
     * @param string $name
     * @param string $value
     */
    public function setMetadataProperty($name, $value);

    /**
     * @return array
     */
    public function getMetadata();

    /**
     * @param int $length
     * @param int $offset
     *
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);

    /**
     * @return int
     */
    public function getVersion();

    /**
     *
     */
    public function increaseVersion();
}
