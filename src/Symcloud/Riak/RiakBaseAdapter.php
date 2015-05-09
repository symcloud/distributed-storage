<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Riak;

use Basho\Riak;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\Response\FetchValueResponse;
use Riak\Client\Command\Kv\Response\StoreValueResponse;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\RiakClient;

abstract class RiakBaseAdapter
{
    /**
     * @var RiakClient
     */
    protected $riak;

    /**
     * RiakBaseAdapter constructor.
     *
     * @param RiakClient $riak
     */
    public function __construct(RiakClient $riak)
    {
        $this->riak = $riak;
    }

    /**
     * @param $key
     * @param RiakNamespace $namespace
     *
     * @return FetchValueResponse
     */
    protected function fetchObject($key, RiakNamespace $namespace)
    {
        $location = new RiakLocation($namespace, $key);

        $fetch = FetchValue::builder($location)->withNotFoundOk(true)->build();

        return $this->riak->execute($fetch);
    }

    /**
     * @param string $key
     * @param string $data
     * @param RiakNamespace $namespace
     *
     * @return StoreValueResponse
     */
    protected function storeObject($key, $data, RiakNamespace $namespace)
    {
        $object = new RiakObject();
        $location = new RiakLocation($namespace, $key);

        $object->setValue($data);
        $object->setContentType('application/json');

        $store = StoreValue::builder($location, $object)->build();

        return $this->riak->execute($store);
    }
}
