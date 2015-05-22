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

class ReferenceField extends AccessorField implements FieldInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * ReferenceField constructor.
     *
     * @param string $name
     * @param string $className
     */
    public function __construct($name, $className)
    {
        parent::__construct($name);

        $this->className = $className;
    }

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     */
    public function getValue(ModelInterface $model)
    {
        $value = parent::getValue($model);

        return $value->getHash();
    }

    /**
     * @param ModelInterface $model
     * @param mixed $value
     * @param DatabaseInterface $database
     */
    public function setValue(ModelInterface $model, $value, DatabaseInterface $database)
    {
        $reference = $database->fetchProxy($value, $this->className);

        parent::setValue($model, $reference, $database);
    }
}
