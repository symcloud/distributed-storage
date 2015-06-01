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
            $value = $field->getValue($model);
            if (is_string($value)) {
                $value = utf8_encode($value);
            }

            $result[$field->getName()] = $value;
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
            $value = array_key_exists($field->getName(), $data) ? $data[$field->getName()] : null;
            if (is_string($value)) {
                $value = utf8_decode($value);
            }

            $field->setValue($model, $value, $database);
        }

        return $model;
    }
}
