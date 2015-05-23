<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Database implements DatabaseInterface
{
    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    /**
     * @var SearchAdapterInterface
     */
    private $searchAdapter;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var PropertyAccessor
     */
    private $accessor;

    /**
     * Database constructor.
     *
     * @param FactoryInterface $factory
     * @param StorageAdapterInterface $storageAdapter
     * @param SearchAdapterInterface $searchAdapter
     * @param MetadataManagerInterface $metadataManager
     */
    public function __construct(
        FactoryInterface $factory,
        StorageAdapterInterface $storageAdapter,
        SearchAdapterInterface $searchAdapter,
        MetadataManagerInterface $metadataManager
    ) {
        $this->factory = $factory;
        $this->storageAdapter = $storageAdapter;
        $this->searchAdapter = $searchAdapter;
        $this->metadataManager = $metadataManager;

        $this->serializer = new Serializer();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function search($searchPattern, $contexts = array())
    {
        return $this->searchAdapter->search($searchPattern, $contexts);
    }

    public function store(ModelInterface $model)
    {
        $metadata = $this->metadataManager->loadByModel($model);
        $data = $this->serializer->serialize($model, $metadata->getDataFields());
        $objectMetadata = $this->serializer->serialize($model, $metadata->getMetadataFields());

        if ($metadata->isHashGenerated()) {
            $hash = $this->factory->createHash(json_encode($data));
        } elseif (!($hash = $model->getHash())) {
            throw new \Exception('Hash not specified');
        }

        $this->storageAdapter->store(
            $hash,
            array(
                'metadata' => $objectMetadata,
                'policy' => $model->getPolicy()->getUsers(),
                'data' => $data,
                'class' => $model->getClass(),
            )
        );

        $this->searchAdapter->index($hash, $model, $metadata);
        $this->accessor->setValue($model, 'hash', $hash);

        return $model;
    }

    public function fetch($hash, $className = null)
    {
        $data = $this->storageAdapter->fetch($hash);
        $metadata = $this->metadataManager->loadByClassname($data['class']);

        if ($className !== null && $data['class'] !== $className) {
            throw new \Exception('Classname not match!');
        }

        $model = $this->getModel($data['class']);
        $this->accessor->setValue($model, 'hash', $hash);
        $this->accessor->setValue($model, 'policy', new Policy($data['policy']));

        $this->serializer->deserialize($model, $data['metadata'], $metadata->getMetadataFields(), $this);
        $this->serializer->deserialize($model, $data['data'], $metadata->getDataFields(), $this);

        return $model;
    }

    public function fetchProxy($hash, $className)
    {
        return $this->factory->createProxy(
            $className,
            function () use ($hash, $className) {
                return $this->fetch($hash, $className);
            }
        );
    }

    public function contains($hash)
    {
        return $this->storageAdapter->contains($hash);
    }

    public function delete($hash)
    {
        $data = $this->fetch($hash);
        $metadata = $this->metadataManager->loadByClassname($data['class']);

        $this->searchAdapter->deindex($hash, $metadata);
        $this->storageAdapter->delete($hash);
    }

    public function deleteAll()
    {
        $this->storageAdapter->deleteAll();
        $this->searchAdapter->deindexAll();
    }

    /**
     * @param string $class
     *
     * @throws \Exception
     *
     * @return ModelInterface
     */
    private function getModel($class)
    {
        $reflectionClass = new \ReflectionClass($class);
        $instance = $reflectionClass->newInstance();
        if (!($instance instanceof ModelInterface)) {
            throw new \Exception('This class is not supported');
        }

        return $instance;
    }
}
