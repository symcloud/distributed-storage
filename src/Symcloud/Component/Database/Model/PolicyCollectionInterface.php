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

interface PolicyCollectionInterface
{
    /**
     * @return PolicyInterface[]
     */
    public function getPolicies();

    /**
     * @param $name
     * @param PolicyInterface $policy
     */
    public function addPolicy($name, PolicyInterface $policy);

    /**
     * @param string $name
     *
     * @return PolicyInterface
     */
    public function getPolicy($name);
}
