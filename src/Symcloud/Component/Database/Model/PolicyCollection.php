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
    public function getPolicies()
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
    public function addPolicy($name, PolicyInterface $policy)
    {
        $this->policies[$name] = $policy;
    }

    /**
     * @param string $name
     *
     * @return PolicyInterface
     */
    public function getPolicy($name)
    {
        return $this->policies[$name];
    }
}
