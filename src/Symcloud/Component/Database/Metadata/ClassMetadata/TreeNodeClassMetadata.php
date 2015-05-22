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
use Symcloud\Component\Database\Metadata\Field\ReadonlyAccessorField;

class TreeNodeClassMetadata extends ClassMetadata
{
    /**
     * BlobFileClassMetadata constructor.
     *
     * @param array $dataFields
     * @param array $metadataFields
     */
    public function __construct(array $dataFields, array $metadataFields)
    {
        parent::__construct(
            array_merge(
                $dataFields,
                array(
                    new AccessorField('name'),
                    new AccessorField('path'),
                    new ReadonlyAccessorField('type'),
                )
            ),
            array_merge(
                $metadataFields,
                array(
                    new AccessorField('name'),
                    new AccessorField('path'),
                    new ReadonlyAccessorField('type'),
                )
            )
        );
    }

    /**
     * @return string
     */
    public function getContext()
    {
        return 'metadata';
    }

    /**
     * @return bool
     */
    public function isHashGenerated()
    {
        return true;
    }
}
