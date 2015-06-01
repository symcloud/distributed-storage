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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class AccessorField extends Field implements FieldInterface
{
    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * AccessorField constructor.
     *
     * @param $name
     */
    public function __construct($name)
    {
        parent::__construct($name);

        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     */
    public function getValue(ModelInterface $model)
    {
        return $this->accessor->getValue($model, $this->getName());
    }

    /**
     * @param ModelInterface $model
     * @param mixed $value
     * @param DatabaseInterface $database
     */
    public function setValue(ModelInterface $model, $value, DatabaseInterface $database)
    {
        $this->accessor->setValue($model, $this->getName(), $value);
    }
}
