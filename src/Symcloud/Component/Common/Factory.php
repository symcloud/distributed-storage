<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Common;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;

class Factory implements FactoryInterface
{
    /**
     * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..).
     *
     * @var string
     */
    private $algorithm;

    /**
     * Shared secret key used for generating the HMAC variant of the message digest.
     *
     * @var string
     */
    private $key;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    /**
     * Factory constructor.
     *
     * @param string                        $algorithm
     * @param string                        $key
     * @param LazyLoadingValueHolderFactory $proxyFactory
     */
    public function __construct($algorithm, $key, LazyLoadingValueHolderFactory $proxyFactory = null)
    {
        $this->algorithm = $algorithm;
        $this->key = $key;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($data)
    {
        return hash_hmac($this->algorithm, $data, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function createFileHash($filePath)
    {
        return hash_hmac_file($this->algorithm, $filePath, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function createProxy($className, callable $initializerCallback)
    {
        if ($this->proxyFactory === null) {
            return $initializerCallback();
        }

        return $this->proxyFactory->createProxy(
            $className,
            function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($initializerCallback) {
                $wrappedObject = $initializerCallback($proxy, $method, $parameters);

                $initializer = null;
            }
        );
    }
}
