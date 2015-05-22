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

use Symcloud\Component\Database\Metadata\ClassMetadataInterface;
use Symcloud\Component\Database\Metadata\Field\FieldInterface;

abstract class ClassMetadata implements ClassMetadataInterface
{
    /**
     * @var FieldInterface[]
     */
    private $dataFields;

    /**
     * @var FieldInterface[]
     */
    private $metadataFields;

    /**
     * ClassMetadata constructor.
     *
     * @param FieldInterface[] $dataFields
     * @param FieldInterface[] $metadataFields
     */
    public function __construct(array $dataFields, array $metadataFields)
    {
        $this->dataFields = $dataFields;
        $this->metadataFields = $metadataFields;
    }

    /**
     * @return FieldInterface[]
     */
    public function getDataFields()
    {
        return $this->dataFields;
    }

    /**
     * @return FieldInterface[]
     */
    public function getMetadataFields()
    {
        return $this->metadataFields;
    }
}
