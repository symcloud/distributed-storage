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

use Symcloud\Component\Database\Metadata\Field\FieldInterface;

interface ClassMetadataInterface
{
    /**
     * @return FieldInterface[]
     */
    public function getDataFields();

    /**
     * @return FieldInterface[]
     */
    public function getMetadataFields();

    /**
     * @return string
     */
    public function getContext();

    /**
     * @return bool
     */
    public function isHashGenerated();
}
