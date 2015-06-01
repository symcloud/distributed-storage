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
use Symcloud\Component\Database\Model\Blob;

class TreeFileClassMetadata extends TreeNodeClassMetadata
{
    /**
     * BlobClassMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                new ReferenceArrayField('blobs', Blob::class),
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
