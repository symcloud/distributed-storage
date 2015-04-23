<?php

namespace Symcloud\Component\MetadataStorage\Model;

interface FileObjectInterface
{
    /**
     * @return string
     */
    public function getFileHash();
}
