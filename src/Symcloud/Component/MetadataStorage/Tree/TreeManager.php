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

class TreeManager implements TreeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTreeWalker(TreeInterface $tree)
    {
        // TODO: Implement getTreeWalker() method.
    }

    /**
     * @param string $hash
     *
     * @return TreeInterface
     */
    public function fetch($hash)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * @param string $hash
     *
     * @return TreeInterface
     */
    public function fetchProxy($hash)
    {
        // TODO: Implement fetchProxy() method.
    }
}
