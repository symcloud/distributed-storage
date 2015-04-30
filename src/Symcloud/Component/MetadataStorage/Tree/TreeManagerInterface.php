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

use Symcloud\Component\MetadataStorage\Model\TreeInterface;

interface TreeManagerInterface
{
    /**
     * @param TreeInterface $tree
     *
     * @return TreeWalkerInterface
     */
    public function getTreeWalker(TreeInterface $tree);

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
     * @return TreeInterface
     */
    public function fetchProxy($hash);
}
