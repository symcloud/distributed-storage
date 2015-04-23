<?php

namespace Symcloud\Component\MetadataStorage\Model;

interface FileObjectInterface extends ObjectInterface
{
    /**
     * @return string
     */
    public function getFileHash();
}
