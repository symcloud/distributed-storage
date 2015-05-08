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

use Symcloud\Component\Common\FactoryInterface;

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
     * @var TreeInterface
     */
    private $parent;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * TreeModel constructor.
     *
     * @param FactoryInterface $factory
     */
    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        if (!$this->hash) {
            $this->hash = $this->factory->createHash(json_encode($this->toArrayForHash()));
        }

        return $this->hash;
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
     * @return TreeInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param TreeInterface $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
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
     *
     */
    public function setDirty()
    {
        $this->hash = null;
        if (!$this->isRoot()) {
            $this->getParent()->setDirty();
        }
    }

    /**
     * @return bool
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
    public function isFile()
    {
        return false;
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

    private function toArrayForHash()
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
