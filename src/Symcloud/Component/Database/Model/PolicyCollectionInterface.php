<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model;

interface PolicyCollectionInterface extends \Serializable
{
    /**
     * @return PolicyInterface[]
     */
    public function getAll();

    /**
     * @param $name
     * @param PolicyInterface $policy
     */
    public function add($name, PolicyInterface $policy);

    /**
     * @param string $name
     *
     * @return PolicyInterface
     */
    public function get($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name);
}
