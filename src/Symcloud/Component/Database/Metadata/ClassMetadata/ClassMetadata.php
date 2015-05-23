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
     * @var string
     */
    private $context;

    /**
     * @var bool
     */
    private $hashGenerated;

    /**
     * ClassMetadata constructor.
     *
     * @param FieldInterface[] $dataFields
     * @param FieldInterface[] $metadataFields
     * @param string $context
     * @param bool $hashGenerated
     */
    public function __construct(array $dataFields, array $metadataFields, $context, $hashGenerated)
    {
        $this->dataFields = $dataFields;
        $this->metadataFields = $metadataFields;
        $this->context = $context;
        $this->hashGenerated = $hashGenerated;
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

    /**
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return bool
     */
    public function isHashGenerated()
    {
        return $this->hashGenerated;
    }
}
