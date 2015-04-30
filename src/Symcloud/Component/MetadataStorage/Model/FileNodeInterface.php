<?php

namespace Symcloud\Component\MetadataStorage\Model;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;

interface FileNodeInterface extends NodeInterface
{
    /**
     * @return BlobFileInterface
     */
    public function getFile();

    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @return string
     */
    public function getName();
}
