<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;

interface TreeManagerInterface
{
    /**
     * @return TreeInterface
     */
    public function createRootTree();

    /**
     * @param string $name
     * @param TreeInterface $parent
     *
     * @return TreeInterface
     */
    public function createTree($name, TreeInterface $parent);

    /**
     * @param string $name
     * @param TreeInterface $parent
     * @param BlobFileInterface $blobFile
     * @param array $metadata
     *
     * @return TreeFileInterface mixed
     */
    public function createTreeFile($name, TreeInterface $parent, BlobFileInterface $blobFile, $metadata = array());

    /**
     * @param TreeInterface $tree
     *
     * @return TreeWalkerInterface
     */
    public function getTreeWalker(TreeInterface $tree);

    /**
     * @param string $path
     * @param string $rootHash
     *
     * @return string
     */
    public function createHash($path, $rootHash);

    /**
     * @param TreeInterface $tree
     */
    public function store(TreeInterface $tree);

    /**
     * @param string $hash
     *
     * @return TreeInterface
     */
    public function fetch($hash);

    /**
     * @param string $hash
     *
     * @return TreeFileInterface
     */
    public function fetchFile($hash);

    /**
     * @param string $hash
     *
     * @return TreeInterface
     */
    public function fetchProxy($hash);

    /**
     * @param string $hash
     *
     * @return TreeFileInterface
     */
    public function fetchFileProxy($hash);
}
