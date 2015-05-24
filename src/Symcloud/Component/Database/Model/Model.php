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

abstract class Model implements ModelInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var PolicyCollectionInterface
     */
    private $policyCollection;

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return PolicyCollectionInterface
     */
    public function getPolicyCollection()
    {
        return $this->policyCollection;
    }

    /**
     * @param PolicyCollectionInterface $policyCollection
     */
    public function setPolicyCollection($policyCollection)
    {
        $this->policyCollection = $policyCollection;
    }
}
