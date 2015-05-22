<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata;

interface MetadataManagerInterface
{
    /**
     * @param object $object
     *
     * @return ClassMetadataInterface
     */
    public function loadByObject($object);

    /**
     * @param string $className
     *
     * @return ClassMetadataInterface
     */
    public function loadByClassname($className);
}
