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
use Basho\Riak\Bucket;

class RiakBaseAdapter
{
    /**
     * @var Riak
     */
    protected $riak;

    /**
     * RiakBaseAdapter constructor.
     *
     * @param Riak $riak
     */
    public function __construct(Riak $riak)
    {
        $this->riak = $riak;
    }

    protected function fetchObject($key, Bucket $bucket)
    {
        return (new Riak\Command\Builder\FetchObject($this->riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    protected function storeObject($key, $data, Bucket $bucket)
    {
        $response = (new Riak\Command\Builder\StoreObject($this->riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();

        return $response;
    }
}
