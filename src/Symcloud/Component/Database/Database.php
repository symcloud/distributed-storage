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
use Symcloud\Component\Database\Metadata\MetadataManager;
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
     */
    public function __construct(
        FactoryInterface $factory,
        StorageAdapterInterface $storageAdapter,
        SearchAdapterInterface $searchAdapter
    ) {
        $this->factory = $factory;
        $this->storageAdapter = $storageAdapter;
        $this->searchAdapter = $searchAdapter;

        $this->metadataManager = new MetadataManager();
        $this->serializer = new Serializer();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function discover($searchPattern)
    {
        // TODO: Implement discover() method.
    }

    public function store(ModelInterface $model)
    {
        $metadata = $this->metadataManager->loadByObject($model);
        $data = $this->serializer->serialize($model, $metadata->getDataFields());
        $objectMetadata = $this->serializer->serialize($model, $metadata->getMetadataFields());
        $hash = $this->factory->createHash($data);

        $this->storageAdapter->store(
            $hash,
            array(
                'metadata' => $objectMetadata,
                'policy' => $model->getPolicy()->getUsers(),
                'data' => $data,
                'class' => get_class($model),
            )
        );

        $this->searchAdapter->index($hash, $model, $metadata);
        $this->accessor->setValue($model, 'hash', $hash);

        return $model;
    }

    public function fetch($hash)
    {
        $data = $this->storageAdapter->fetch($hash);
        $metadata = $this->metadataManager->loadByClassname($data['class']);

        $model = $this->getModel($data['class']);
        $this->accessor->setValue($model, 'hash', $hash);
        $this->accessor->setValue($model, 'policy', new Policy($data['policy']));

        $this->serializer->deserialize($model, $data['metadata'], $metadata->getMetadataFields());
        $this->serializer->deserialize($model, $data['data'], $metadata->getDataFields());

        return $hash;
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
     * @return ModelInterface
     */
    private function getModel($class)
    {
        $reflectionClass = new \ReflectionClass($class);

        return $reflectionClass->newInstance();
    }
}
