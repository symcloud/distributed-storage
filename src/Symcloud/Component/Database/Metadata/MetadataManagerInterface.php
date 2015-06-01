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

use Symcloud\Component\Database\Model\ModelInterface;

interface MetadataManagerInterface
{
    /**
     * @param ModelInterface $object
     *
     * @return ClassMetadataInterface
     */
    public function loadByModel(ModelInterface $object);

    /**
     * @param string $className
     *
     * @return ClassMetadataInterface
     */
    public function loadByClassname($className);
}
