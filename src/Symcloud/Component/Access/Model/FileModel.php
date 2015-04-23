<?php

namespace Symcloud\Component\Access\Model;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;

class FileModel implements FileInterface
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @var BlobFileInterface
     */
    private $data;

    /**
     * @var FileObjectInterface
     */
    private $object;

    public function getTitle()
    {
        return $this->metadata->getTitle();
    }

    public function getDescription()
    {
        return $this->metadata->getDescription();
    }

    public function getMetadataStore()
    {
        return $this->metadata->getKeyValueStore();
    }

    public function getFileHash()
    {
        return $this->data->getHash();
    }

    public function getPath()
    {
        return sprintf('%s/%s', $this->object->getParent(), $this->object->getName());
    }

    public function getDepth()
    {
        return $this->object->getDepth();
    }

    public function getContent($length = -1, $offset = 0)
    {
        return $this->data->getContent($length, $offset);
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param MetadataInterface $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @return BlobFileInterface
     */
    public function getData()
    {
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return FileObjectInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param FileObjectInterface $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }
}
