<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Replication;

use Symcloud\Component\Database\Event\DatabaseFetchEvent;
use Symcloud\Component\Database\Event\DatabaseStoreEvent;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\DistributedModelInterface;
use Symcloud\Component\Database\Model\PolicyCollectionInterface;
use Symcloud\Component\Database\Replication\Exception\NotPrimaryServerException;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;

class Replicator implements ReplicatorInterface
{
    const POLICY_NAME = 'replicator';

    /**
     * @var ApiInterface
     */
    private $api;

    /**
     * @var ServerInterface
     */
    private $primaryServer;

    /**
     * @var ServerInterface[]
     */
    private $servers = array();

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    /**
     * @var SearchAdapterInterface
     */
    private $searchAdapter;

    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var array
     */
    private $options;

    /**
     * Replicator constructor.
     *
     * @param ApiInterface $api
     * @param StorageAdapterInterface $storageAdapter
     * @param SearchAdapterInterface $searchAdapter
     * @param MetadataManagerInterface $metadataManager
     * @param ServerInterface $primaryServer
     * @param ServerInterface[] $servers
     * @param array $options
     */
    public function __construct(
        ApiInterface $api,
        StorageAdapterInterface $storageAdapter,
        SearchAdapterInterface $searchAdapter,
        MetadataManagerInterface $metadataManager,
        ServerInterface $primaryServer,
        array $servers,
        array $options = array()
    ) {
        $this->api = $api;
        $this->storageAdapter = $storageAdapter;
        $this->searchAdapter = $searchAdapter;
        $this->metadataManager = $metadataManager;
        $this->primaryServer = $primaryServer;
        $this->servers = $servers;

        $this->setOptions($options);
    }

    private function setOptions($options)
    {
        $this->options = array_merge(
            array('n' => 2),
            $options
        );
    }

    public function onStore(DatabaseStoreEvent $event)
    {
        // TODO store not new but changed not on the primary server

        $model = $event->getModel();
        if (!$event->isNew() ||
            !($model instanceof DistributedModelInterface) ||
            ($policyCollection = $model->getPolicyCollection()) === null ||
            $policyCollection->has(self::POLICY_NAME)
        ) {
            return;
        }

        // TODO determine primary and backup server
        // TODO if this is backup server store it as "backup" which is readonly
        // if store called with 'replication' => 'none' (...) then store stubs on all no server (for ???)
        // TODO if store called with 'replication' => 'security' (...) then store stubs on all known servers which are allowed to see (for references)
        // if store called with 'replication' => 'full' (...) then store backup on 2 (configurable) servers and stubs on all known servers (for blobs)

        $option = $event->getOption(self::OPTION_NAME, self::TYPE_NONE);

        switch ($option) {
            case self::TYPE_FULL:
                $policy = $this->getFullReplicatorPolicy();
                break;
            default:
                return;
        }

        $policyCollection->add(self::POLICY_NAME, $policy);
        $this->distribute($model, $event->getData(), $policy);
    }

    /**
     * @return ReplicatorPolicy
     */
    private function getFullReplicatorPolicy()
    {
        $backups = array();

        /** @var int $n maximum number of replications */
        $n = $this->options['n'];
        if (sizeof($this->servers) < $n) {
            $n = sizeof($this->servers);
        }

        for ($i = 0; $i < $n; $i++) {
            $backups[] = $this->servers[rand(0, sizeof($this->servers) - 1)];
        }

        return new ReplicatorPolicy($this->primaryServer, $backups);
    }

    private function distribute(DistributedModelInterface $model, $data, ReplicatorPolicy $policy)
    {
        $backupData = array(
            'type' => 'backup',
            'class' => $model->getClass(),
            'data' => $data,
            'policies' => serialize($model->getPolicyCollection()),
        );

        // TODO if server is not available?
        foreach ($policy->getBackupServers() as $server) {
            $this->api->store($model->getHash(), $backupData, $server);
        }
    }

    public function onFetch(DatabaseFetchEvent $event)
    {
        // TODO if it is not on the server search for it (go through list of servers)
        // TODO determine is primary (no TODO), backup server (no TODO) or stub
        // TODO if it is stub: load data from random server (out of primary and backups)
    }

    public function storeStub($hash, ServerInterface $primaryServer, array $backupServers)
    {
    }

    public function store($hash, $data)
    {
        $classMetadata = $this->metadataManager->loadByClassname($data['class']);
        $this->storageAdapter->store($hash, $data, $classMetadata->getContext());
        $this->searchAdapter->indexObject($hash, $data, $classMetadata);
    }

    public function fetch($hash, $class, $username)
    {
        $classMetadata = $this->metadataManager->loadByClassname($class);
        $data = $this->storageAdapter->fetch($hash, $classMetadata->getContext());

        if (array_key_exists('type', $data) && $data['type'] === 'backup') {
            /** @var PolicyCollectionInterface $policyCollection */
            $policyCollection = unserialize($data['policies']);

            /** @var ReplicatorPolicy $policy */
            $policy = $policyCollection->get(self::POLICY_NAME);
            throw new NotPrimaryServerException($policy->getPrimaryServer());
        }

        // TODO security-checker with given username ("%s::%s", server, username)

        return $data;
    }
}
