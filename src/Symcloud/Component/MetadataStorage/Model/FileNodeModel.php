<?php

namespace Symcloud\Component\MetadataStorage\Model;

class FileNodeModel implements FileNodeInterface
{
    /**
     * {@inheritdoc}
     */
    public function getHash()
    {
        // TODO: Implement getHash() method.
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        // TODO: Implement setHash() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getFile()
    {
        // TODO: Implement getFile() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata()
    {
        // TODO: Implement getMetadata() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        // TODO: Implement getRoot() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        // TODO: Implement getPath() method.
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return array(
            self::TYPE_KEY => 'file',
            self::FILE_KEY => $this->getFile()->getHash(),
            self::PATH_KEY => $this->getPath(),
            self::ROOT_KEY => $this->getRoot()->getHash(),
            self::METADATA_KEY => $this->getMetadata()->getHash(),
        );
    }

    /**
     * {@inheritdoc}
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
