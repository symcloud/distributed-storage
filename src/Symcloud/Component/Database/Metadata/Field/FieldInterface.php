<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata\Field;

use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\ModelInterface;

interface FieldInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     */
    public function getValue(ModelInterface $model);

    /**
     * @param ModelInterface $model
     * @param mixed $value
     * @param DatabaseInterface $database
     */
    public function setValue(ModelInterface $model, $value, DatabaseInterface $database);
}
