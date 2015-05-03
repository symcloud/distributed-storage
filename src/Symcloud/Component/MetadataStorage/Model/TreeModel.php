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

class TreeModel implements TreeInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var TreeInterface
     */
    private $root;

    /**
     * @var string
     */
    private $path;

    /**
     * @var NodeInterface[]
     */
    private $children;

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

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
     * @param string $name
     * @param NodeInterface $node
     */
    public function setChild($name, NodeInterface $node)
    {
        $this->hash = null;
        $this->children[$name] = $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @param TreeInterface $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return boolean
     */
    public function isRoot()
    {
        return $this === $this->getRoot();
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
        );
        foreach ($this->getChildren() as $name => $child) {
            $children[$child->getType()][$name] = $child->getHash();
        }

        return array(
            self::TYPE_KEY => $this->getType(),
            self::PATH_KEY => $this->getPath(),
            self::ROOT_KEY => $this->getRoot()->getHash(),
            self::CHILDREN_KEY => $children,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
