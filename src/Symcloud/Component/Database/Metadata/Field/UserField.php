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
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserField extends AccessorField implements FieldInterface
{
    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * UserField constructor.
     *
     * @param string $name
     * @param UserProviderInterface $userProvider
     */
    public function __construct($name, UserProviderInterface $userProvider)
    {
        parent::__construct($name);

        $this->userProvider = $userProvider;
    }

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     */
    public function getValue(ModelInterface $model)
    {
        $value = parent::getValue($model);

        return $value->getUsername();
    }

    /**
     * @param ModelInterface $model
     * @param mixed $value
     * @param DatabaseInterface $database
     */
    public function setValue(ModelInterface $model, $value, DatabaseInterface $database)
    {
        parent::setValue($model, $this->userProvider->loadUserByUsername($value), $database);
    }
}
