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

class PolicyCollection implements PolicyCollectionInterface
{
    /**
     * @var PolicyInterface[]
     */
    private $policies = array();

    /**
     * @return PolicyInterface[]
     */
    public function getAll()
    {
        return $this->policies;
    }

    /**
     * @param PolicyInterface[] $policies
     */
    public function setPolicies($policies)
    {
        $this->policies = $policies;
    }

    /**
     * @param $name
     * @param PolicyInterface $policy
     */
    public function add($name, PolicyInterface $policy)
    {
        $this->policies[$name] = $policy;
    }

    /**
     * @param string $name
     *
     * @return PolicyInterface
     */
    public function get($name)
    {
        return $this->policies[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->policies);
    }
}