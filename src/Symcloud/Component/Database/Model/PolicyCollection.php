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
     * PolicyCollection constructor.
     *
     * @param PolicyInterface[] $policies
     */
    public function __construct(array $policies = array())
    {
        $this->policies = $policies;
    }

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

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $policies = array();
        $all = $this->getAll();
        if (is_array($all)) {
            foreach ($this->getAll() as $name => $policy) {
                $policies[$name] = serialize($policy);
            }
        }

        return serialize(array('policies' => $policies));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        foreach ($data['policies'] as $name => $policyData) {
            $this->add($name, unserialize($policyData));
        }
    }
}
