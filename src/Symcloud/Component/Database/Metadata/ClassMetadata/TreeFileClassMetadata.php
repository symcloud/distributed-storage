<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata\ClassMetadata;

use Symcloud\Component\Database\Metadata\Field\AccessorField;
use Symcloud\Component\Database\Metadata\Field\ReferenceArrayField;
use Symcloud\Component\Database\Model\Chunk;

class TreeFileClassMetadata extends TreeNodeClassMetadata
{
    /**
     * ChunkClassMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                new ReferenceArrayField('chunks', Chunk::class),
            ),
            array(
                new AccessorField('metadata'),
                new AccessorField('version'),
                new AccessorField('fileHash'),
                new AccessorField('size'),
                new AccessorField('mimetype'),
            )
        );
    }
}
