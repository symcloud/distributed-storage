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
     * {@inheritdoc}
     */
    public function removeChild($name)
    {
        unset($this->children[$name]);
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
        $children = array(
            NodeInterface::TREE_TYPE => array(),
            NodeInterface::FILE_TYPE => array(),
            NodeInterface::REFERENCE_TYPE => array(),
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
        $children = $this->getChildren();
        $this->children = array();
        foreach ($children as $name => $child) {
            $newChild = clone $child;
            $this->children[$name] = $newChild;
        }
    }
}
