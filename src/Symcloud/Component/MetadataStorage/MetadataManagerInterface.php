<?php

namespace Symcloud\Component\MetadataStorage;

use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;

interface MetadataManagerInterface
{
    /**
     * @param FileObjectInterface $object
     * @return MetadataInterface
     */
    public function getByObject($object);
}
