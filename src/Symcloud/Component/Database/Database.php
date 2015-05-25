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
use Symcloud\Component\Database\Event\DatabaseEvent;
use Symcloud\Component\Database\Event\DatabaseStoreEvent;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\DistributedModelInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        FactoryInterface $factory,
        StorageAdapterInterface $storageAdapter,
        SearchAdapterInterface $searchAdapter,
        MetadataManagerInterface $metadataManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->factory = $factory;
        $this->storageAdapter = $storageAdapter;
        $this->searchAdapter = $searchAdapter;
        $this->metadataManager = $metadataManager;
        $this->eventDispatcher = $eventDispatcher;

        $this->serializer = new Serializer();
        $this->accessor = PropertyAccess::createPropertyAccessor();
    }

    public function search($searchPattern, $contexts = array())
    {
        return $this->searchAdapter->search($searchPattern, $contexts);
    }

    public function store(ModelInterface $model, array $options = array())
    {
        // determine metadata and data from model
        $metadata = $this->metadataManager->loadByModel($model);
        $data = $this->serializer->serialize(
            $model,
            array_merge($metadata->getDataFields(), $metadata->getMetadataFields())
        );

        // generate hash if necessary
        if ($metadata->isHashGenerated()) {
            $hash = $this->factory->createHash(json_encode($data));
        } elseif (!($hash = $model->getHash())) {
            throw new \Exception('Hash not specified');
        }

        $this->accessor->setValue($model, 'hash', $hash);
        $isNew = $this->contains($hash, $model->getClass());

        // dispatch event
        $event = new DatabaseStoreEvent($model, $data, $isNew,  $metadata, $options);
        $this->eventDispatcher->dispatch(DatabaseEvent::STORE_EVENT, $event);

        // possibility to cancel store in a event-handler
        if ($event->isCanceled()) {
            return;
        }

        // possibility to change model in the event-handler to a stub
        $model = $event->getModel();
        $data = $event->getData();

        $object = array(
            'data' => $data,
            'class' => $model->getClass(),
        );

        if ($model instanceof DistributedModelInterface) {
            $object['policies'] = serialize($model->getPolicyCollection());
        }

        $this->storageAdapter->store($hash, $object, $metadata->getContext());
        $this->searchAdapter->index($hash, $model, $metadata);

        return $model;
    }

    public function fetch($hash, $className)
    {
        $metadata = $this->metadataManager->loadByClassname($className);
        $data = $this->storageAdapter->fetch($hash, $metadata->getContext());

        if ($data['class'] !== $className) {
            throw new \Exception('Classname not match!');
        }

        $model = $this->getModel($data['class']);
        $this->accessor->setValue($model, 'hash', $hash);

        if ($model instanceof DistributedModelInterface) {
            $this->accessor->setValue($model, 'policyCollection', unserialize($data['policies']));
        }

        $this->serializer->deserialize(
            $model,
            $data['data'],
            array_merge($metadata->getDataFields(), $metadata->getMetadataFields()),
            $this
        );

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

    public function contains($hash, $className)
    {
        $metadata = $this->metadataManager->loadByClassname($className);

        return $this->storageAdapter->contains($hash, $metadata->getContext());
    }

    public function delete($hash, $className)
    {
        $metadata = $this->metadataManager->loadByClassname($className);

        $this->searchAdapter->deindex($hash, $metadata);
        $this->storageAdapter->delete($hash, $metadata->getContext());
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
