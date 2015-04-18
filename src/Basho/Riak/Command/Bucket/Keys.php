<?php

namespace Basho\Riak\Command\Bucket;

use Basho\Riak\Command;
use Basho\Riak\CommandInterface;

/**
 * Used to get all keys from Riak
 */
class Keys extends Command\Object implements CommandInterface
{
    protected $method = 'GET';

    public function __construct(Command\Builder\FetchObject $builder)
    {
        parent::__construct($builder);

        $this->bucket = $builder->getBucket();
        $this->location = $builder->getLocation();
    }

    public function getParameters()
    {
        return array(
            'keys' => 'true'
        );
    }

    public function hasParameters()
    {
        return true;
    }
}
