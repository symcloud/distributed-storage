<?php

namespace Integration\Parts;

use GuzzleHttp\Exception\ClientException;
use Riak\Client\Command\Kv\DeleteValue;
use Riak\Client\Command\Kv\FetchValue;
use Riak\Client\Command\Kv\ListKeys;
use Riak\Client\Command\Kv\Response\FetchValueResponse;
use Riak\Client\Command\Kv\Response\ListKeysResponse;
use Riak\Client\Command\Kv\StoreValue;
use Riak\Client\Core\Query\RiakLocation;
use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\Core\Query\RiakObject;
use Riak\Client\RiakClient;
use Riak\Client\RiakClientBuilder;

trait RiakTrait
{
    /**
     * @var RiakClient
     */
    private $riak;

    protected function getRiak()
    {
        if (!$this->riak) {
            $builder = new RiakClientBuilder();
            $this->riak = $builder->withNodeUri('http://localhost:8098')->build();
        }

        return $this->riak;
    }

    protected function clearBucket(RiakNamespace $namespace)
    {
        $response = $this->fetchBucketKeys($namespace);

        foreach ($response as $key) {
            try {
                $this->deleteObject($key, $namespace);
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * @param RiakNamespace $namespace
     * @return string[]
     */
    protected function fetchBucketKeys(RiakNamespace $namespace)
    {
        $fetch = ListKeys::builder($namespace)->build();

        $keys = array();
        /** @var ListKeysResponse $response */
        $response = $this->getRiak()->execute($fetch);
        foreach ($response->getIterator() as $location) {
            $keys[] = $location->getKey();
        }

        return $keys;
    }

    /**
     * @param $key
     * @param RiakNamespace $namespace
     * @return FetchValueResponse
     */
    protected function fetchObject($key, RiakNamespace $namespace)
    {
        $location = new RiakLocation($namespace, $key);
        $fetch = FetchValue::builder($location)->build();

        return $this->getRiak()->execute($fetch);
    }

    protected function storeObject($key, $data, RiakNamespace $namespace)
    {
        $object = new RiakObject();
        $location = new RiakLocation($namespace, $key);

        if(!is_string($data)){
            $data = json_encode($data);
        }

        $object->setValue($data);
        $object->setContentType('application/json');

        $store = StoreValue::builder($location, $object)->build();

        return $this->getRiak()->execute($store);
    }

    protected function deleteObject($key, RiakNamespace $namespace)
    {
        $location = new RiakLocation($namespace, $key);

        $delete = DeleteValue::builder($location)->build();

        return $this->getRiak()->execute($delete);
    }
}
