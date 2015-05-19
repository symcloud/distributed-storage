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

use Symcloud\Component\FileStorage\Model\BlobFileInterface;

class TreeFileModel extends BaseTreeModel implements TreeFileInterface
{
    /**
     * @var BlobFileInterface
     */
    private $file;

    /**
     * @var array
     */
    private $metadata = array();

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $version;

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
    public function getFileHash()
    {
        return $this->getFile()->getHash();
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(BlobFileInterface $file)
    {
        $this->file = $file;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function increaseVersion()
    {
        $this->version += 1;
    }

    /**
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
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
    public function toArray()
    {
        return array(
            self::TYPE_KEY => self::FILE_TYPE,
            self::FILE_KEY => $this->getFile()->getHash(),
            self::PATH_KEY => $this->getPath(),
            self::METADATA_KEY => $this->getAllMetadata(),
            self::VERSION_KEY => $this->getVersion(),
        );
    }
}
