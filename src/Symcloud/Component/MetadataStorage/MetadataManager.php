<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage;

use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;

class MetadataManager implements MetadataManagerInterface
{
    /**
     * @param FileObjectInterface $object
     *
     * @return MetadataInterface
     */
    public function getByObject($object)
    {
        // TODO: Implement getByObject() method.
    }
}
