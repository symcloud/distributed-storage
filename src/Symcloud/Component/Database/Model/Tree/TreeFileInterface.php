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

use Symcloud\Component\Database\Model\BlobFileInterface;

interface TreeFileInterface extends TreeNodeInterface
{
    /**
     * @return string
     */
    public function getFileHash();

    /**
     * @return BlobFileInterface
     */
    public function getFile();

    /**
     * @param BlobFileInterface $file
     */
    public function setFile(BlobFileInterface $file);

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
     * @return int
     */
    public function getVersion();

    /**
     *
     */
    public function increaseVersion();
}
