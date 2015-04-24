<?php

namespace Integration\Parts;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;

trait FactoryTrait
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    protected function getProxyFactory()
    {
        if (!$this->proxyFactory) {
            $this->proxyFactory = new LazyLoadingValueHolderFactory();
        }

        return $this->proxyFactory;
    }

    protected function getFactory()
    {
        if (!$this->factory) {
            $this->factory = new Factory('md5', 'ThisIsMySecretValue', $this->getProxyFactory());
        }

        return $this->factory;
    }

}
