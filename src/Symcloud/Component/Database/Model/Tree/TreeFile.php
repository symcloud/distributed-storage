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

use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;

class TreeFile extends TreeNode implements TreeFileInterface
{
    /**
     * @var array
     */
    private $metadata = array();

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $fileHash;

    /**
     * @var int
     */
    private $size;

    /**
     * @var string
     */
    private $mimetype;

    /**
     * @var BlobInterface[]
     */
    private $blobs;

    /**
     * {@inheritdoc}
     */
    public function getFileHash()
    {
        return $this->fileHash;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype()
    {
        return $this->mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlobs()
    {
        return $this->blobs;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(BlobFileInterface $file)
    {
        $this->fileHash = $file->getHash();
        $this->mimetype = $file->getMimetype();
        $this->size = $file->getSize();
        $this->blobs = $file->getBlobs();
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
    public function getMetadataProperty($name)
    {
        return ($this->hasMetadata($name) ? $this->metadata[$name] : null);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetadataProperty($name, $value)
    {
        if ($this->getMetadataProperty($name) === $value) {
            return;
        }

        $this->metadata[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    public function getContent($length = -1, $offset = 0)
    {
        if ($length !== -1 || $offset !== 0) {
            throw new \Exception('Not implemented');
        }

        $content = '';
        foreach ($this->getBlobs() as $blob) {
            $content .= $blob->getData();
        }

        return $content;
    }

    /**
     * @param array $metadata
     */
    public function setAllMetadata(array $metadata)
    {
        foreach ($metadata as $name => $value) {
            $this->setMetadataProperty($name, $value);
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
     * @param string $fileHash
     */
    public function setFileHash($fileHash)
    {
        $this->fileHash = $fileHash;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @param string $mimetype
     */
    public function setMimetype($mimetype)
    {
        $this->mimetype = $mimetype;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return self::FILE_TYPE;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }
}
