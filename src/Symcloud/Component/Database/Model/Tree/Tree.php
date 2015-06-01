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

class Tree extends TreeNode implements TreeInterface
{
    /**
     * @var TreeNodeInterface[]
     */
    private $children = array();

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param TreeNodeInterface[] $children
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
     * {@inheritdoc}
     */
    public function setChild($name, TreeNodeInterface $node)
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
     * @return string
     */
    public function getClass()
    {
        return self::class;
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
