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

abstract class BaseTreeModel implements NodeInterface
{
    /**
     * @var string
     */
    protected $hash;

    /**
     * @var TreeInterface
     */
    private $root;

    /**
     * @var TreeInterface
     */
    private $parent;

    /**
     * @var string
     */
    private $path;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * BaseTreeModel constructor.
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
     * @return array
     */
    abstract protected function toArrayForHash();

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
    public function setRoot(TreeInterface $root)
    {
        $this->setDirty();
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
    public function setParent(TreeInterface $parent)
    {
        $this->setDirty();
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
     * {@inheritdoc}
     */
    public function isFile()
    {
        return $this->getType() === self::FILE_TYPE;
    }

    /**
     *
     */
    public function setDirty()
    {
        $this->hash = null;
        if ($this->getParent() !== null) {
            $this->getParent()->setDirty();
        }
    }
}
