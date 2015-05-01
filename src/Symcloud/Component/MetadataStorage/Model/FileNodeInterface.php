<?php

namespace Symcloud\Component\MetadataStorage\Model;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;

interface FileNodeInterface extends NodeInterface
{
    const FILE_KEY = 'file';
    const METADATA_KEY = 'metadata';

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
