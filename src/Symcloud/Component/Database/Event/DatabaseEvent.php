<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Event;

use Symcloud\Component\Database\Model\ModelInterface;
use Symfony\Component\EventDispatcher\Event;

abstract class DatabaseEvent extends Event
{
    const STORE_EVENT = 'symcloud.database.store';
    const FETCH_EVENT = 'symcloud.database.fetch';

    /**
     * @var ModelInterface
     */
    protected $model;

    /**
     * @var bool
     */
    protected $canceled = false;

    /**
     * DatabaseEvent constructor.
     *
     * @param ModelInterface $model
     */
    public function __construct(ModelInterface $model)
    {
        $this->model = $model;
    }

    /**
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    public function cancel()
    {
        $this->canceled = true;
    }

    /**
     * @return bool
     */
    public function isCanceled()
    {
        return $this->canceled;
    }
}
