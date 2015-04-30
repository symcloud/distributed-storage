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
    function jsonSerialize()
    {
        return array(
            'file' => $this->getFile()->getHash(),
            'path' => $this->getPath(),
            'metadata' => $this->getMetadata()->getHash(),
        );
    }
}
