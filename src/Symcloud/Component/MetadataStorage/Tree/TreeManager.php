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

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class TreeManager implements TreeManagerInterface
{
    /**
     * @var TreeAdapterInterface
     */
    private $treeAdapter;

    /**
     * @var FactoryInterface
     */
    private $factory;

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

            $this->storeNode($child);
        }

        $this->storeNode($tree);
    }

    /**
     * @param NodeInterface $child
     */
    private function storeNode(NodeInterface $child)
    {
        if ($child->getHash() === null) {
            $child->setHash($this->factory->createHash(json_encode($child)));
        }

        $this->treeAdapter->store($child->getHash(), $child->toArray());
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
