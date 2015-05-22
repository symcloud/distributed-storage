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

class BlobFileClassMetadata extends ClassMetadata
{
    /**
     * BlobFileClassMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                new ReferenceArrayField('blobs'),
            ),
            array(
                new AccessorField('size'),
                new AccessorField('mimetype'),
            )
        );
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return 'file';
    }

    /**
     * @return bool
     */
    public function isHashGenerated()
    {
        return false;
    }
}
