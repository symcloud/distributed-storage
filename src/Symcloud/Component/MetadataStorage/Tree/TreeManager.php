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

use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class TreeManager implements TreeManagerInterface
{
    /**
     * @var TreeAdapterInterface
     */
    private $treeAdapter;

    /**
     * {@inheritdoc}
     */
    public function getTreeWalker(TreeInterface $tree)
    {
        return new MaterializedPathTreeWalker($tree, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function store(TreeInterface $tree)
    {
        foreach ($tree->getChildren() as $child) {
            if ($child instanceof TreeInterface) {
                $this->store($child);
            }

            $this->storeFile($child);
        }

        // TODO store tree
    }

    /**
     * @param NodeInterface $child
     */
    private function storeFile(NodeInterface $child)
    {
        // TODO store file
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * {@inheritdoc}
     */
    public function fetchProxy($hash)
    {
        // TODO: Implement fetchProxy() method.
    }
}
