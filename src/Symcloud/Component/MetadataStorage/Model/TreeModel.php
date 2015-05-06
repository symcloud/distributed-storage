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
        return $this->factory->createHash(json_encode($this));
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
        $children = array(
            NodeInterface::TREE_TYPE => array(),
            NodeInterface::FILE_TYPE => array(),
        );
        foreach ($this->getChildren() as $name => $child) {
            $children[$child->getType()][$name] = $child->getHash();
        }

        $rootHash = null;
        if (!$this->isRoot()) {
            $this->getRoot()->getHash();
        }

        return array(
            self::TYPE_KEY => $this->getType(),
            self::PATH_KEY => $this->getPath(),
            self::ROOT_KEY => $rootHash,
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
