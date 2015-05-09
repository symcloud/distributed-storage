<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

class TreeModel extends BaseTreeModel implements TreeInterface
{
    /**
     * @var NodeInterface[]
     */
    private $children;

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param NodeInterface[] $children
     */
    public function setChildren($children)
    {
        $this->setDirty();
        $this->children = $children;
    }

    /**
     * {@inheritdoc}
     */
    public function getChild($name)
    {
        if (!array_key_exists($name, $this->children)) {
            return;
        }

        return $this->children[$name];
    }

    /**
     * @param string $name
     * @param NodeInterface $node
     */
    public function setChild($name, NodeInterface $node)
    {
        $this->children[$name] = $node;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->getParent() === null;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::TREE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $rootHash = null;
        $parentHash = null;
        if (!$this->isRoot()) {
            $rootHash = $this->getRoot()->getHash();
            $parentHash = $this->getParent()->getHash();
        }

        return array_merge(
            array(
                self::ROOT_KEY => $rootHash,
                self::PARENT_KEY => $parentHash,
            ),
            $this->toArrayForHash()
        );
    }

    protected function toArrayForHash()
    {
        $children = array(
            NodeInterface::TREE_TYPE => array(),
            NodeInterface::FILE_TYPE => array(),
        );

        foreach ($this->getChildren() as $name => $child) {
            $children[$child->getType()][$name] = $child->getHash();
        }

        return array(
            self::TYPE_KEY => $this->getType(),
            self::PATH_KEY => $this->getPath(),
            self::CHILDREN_KEY => $children,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->hash = null;
        if (!$this->isRoot()) {
            $this->setRoot($this->getParent()->getRoot());
        } else {
            $this->setRoot($this);
        }

        $children = $this->getChildren();
        $this->children = array();
        foreach ($children as $name => $child) {
            $newChild = clone $child;
            $newChild->setParent($this);
            $newChild->setRoot($this->getRoot());
            $this->children[$name] = $newChild;
        }
    }
}
