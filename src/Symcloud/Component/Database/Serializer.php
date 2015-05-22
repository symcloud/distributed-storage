<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database;

use Symcloud\Component\Database\Metadata\Field\FieldInterface;
use Symcloud\Component\Database\Model\ModelInterface;

class Serializer
{
    /**
     * @param ModelInterface $model
     * @param FieldInterface[] $fields
     *
     * @return array
     */
    public function serialize(ModelInterface $model, $fields)
    {
        $result = array();

        foreach ($fields as $field) {
            $result[$field->getName()] = $field->getValue($model);
        }

        return $result;
    }

    /**
     * @param ModelInterface $model
     * @param array $data
     * @param FieldInterface[] $fields
     * @param DatabaseInterface $database
     *
     * @return ModelInterface
     */
    public function deserialize(ModelInterface $model, $data, $fields, DatabaseInterface $database)
    {
        foreach ($fields as $field) {
            $field->setValue($model, $data[$field->getName()], $database);
        }

        return $model;
    }
}
