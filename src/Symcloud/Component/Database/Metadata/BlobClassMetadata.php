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

class BlobClassMetadata extends ClassMetadata
{
    /**
     * BlobClassMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                new AccessorField('data'),
            ),
            array(
                new AccessorField('length'),
            )
        );
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return 'blob';
    }

    public function isHashGenerated()
    {
        return false;
    }
}
