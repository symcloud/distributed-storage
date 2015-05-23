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

class ReferenceArrayField extends AccessorField
{
    /**
     * @param ModelInterface $model
     *
     * @return mixed
     */
    public function getValue(ModelInterface $model)
    {
        $references = parent::getValue($model);

        $result = array();
        foreach ($references as $key => $reference) {
            $result[$key] = array('hash' => $reference->getHash(), 'class' => $reference->getClass());
        }

        return $result;
    }

    /**
     * @param ModelInterface $model
     * @param mixed $references
     * @param DatabaseInterface $database
     */
    public function setValue(ModelInterface $model, $references, DatabaseInterface $database)
    {
        $result = array();
        foreach ($references as $key => $reference) {
            $result[$key] = $database->fetchProxy($reference['hash'], $reference['class']);
        }

        parent::setValue($model, $result, $database);
    }
}
