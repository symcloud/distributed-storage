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
use Symcloud\Component\FileStorage\Model\BlobFileInterface;

class TreeFileModel implements TreeFileInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var BlobFileInterface
     */
    private $file;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var string
     */
    private $name;

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
     * TreeFileModel constructor.
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

    public function setDirty()
    {
        $this->hash = null;

        $this->getParent()->setDirty();
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(BlobFileInterface $file)
    {
        $this->file = $file;

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * {@inheritdoc}
     */
    public function hasMetadata($name)
    {
        return array_key_exists($name, $this->metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($name)
    {
        return ($this->hasMetadata($name) ? $this->metadata[$name] : null);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadata($name, $value)
    {
        if ($this->getMetadata($name) === $value) {
            return;
        }

        $this->metadata[$name] = $value;

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setAllMetadata(array $metadata)
    {
        foreach ($metadata as $name => $value) {
            $this->setMetadata($name, $value);
        }

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::FILE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function isFile()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            self::TYPE_KEY => self::FILE_TYPE,
            self::FILE_KEY => $this->getFile()->getHash(),
            self::PATH_KEY => $this->getPath(),
            self::ROOT_KEY => $this->getRoot()->getHash(),
            self::METADATA_KEY => $this->getAllMetadata(),
            self::PARENT_KEY => $this->getParent()->getHash(),
        );
    }

    /**
     * data which will be used to generate hash.
     */
    private function toArrayForHash()
    {
        return array(
            self::TYPE_KEY => self::FILE_TYPE,
            self::FILE_KEY => $this->getFile()->getHash(),
            self::PATH_KEY => $this->getPath(),
            self::METADATA_KEY => $this->getAllMetadata(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __clone()
    {
        $this->hash = null;
    }
}
