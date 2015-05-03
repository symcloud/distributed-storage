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
use Symcloud\Component\MetadataStorage\Exception\NotATreeException;
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

        $this->treeAdapter->store($child->getHash(), $child);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        $data = $this->treeAdapter->fetch($hash);

        $path = $data[NodeInterface::PATH_KEY];

        if ($data[TreeInterface::TYPE_KEY] !== NodeInterface::TREE_TYPE) {
            throw new NotATreeException($hash, $path);
        }

        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);

        $children = array();
        foreach ($data[TreeInterface::CHILDREN_KEY][TreeInterface::TREE_TYPE] as $childHash) {
            $children[] = $this->fetchProxy($childHash);
        }
        foreach ($data[TreeInterface::CHILDREN_KEY][TreeInterface::FILE_TYPE] as $childHash) {
            $children[] = $this->fetchProxy($childHash);
        }

        return $this->factory->createTree($path, $root, $children, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchProxy($hash)
    {
        return $this->factory->createProxy(
            TreeInterface::class,
            function () use ($hash) {
                return $this->fetch($hash);
            }
        );
    }
}
