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

class Replicator implements ReplicatorInterface
{
    const POLICY_NAME = 'replicator';

    /**
     * @var ServerInterface[]
     */
    private $servers = array();

    public function onStore(DatabaseStoreEvent $event)
    {
        $model = $event->getModel();
        $policyCollection = $model->getPolicyCollection();

        // TODO determine primary and backup server
        // TODO store on backup servers
        // TODO if this is backup server store it as "backup" which is readonly
        // TODO if store called with 'stub' (...) then store subs on all known servers (for references)

        if (!$policyCollection->has(self::POLICY_NAME)) {
            $policyCollection->add(self::POLICY_NAME, new ReplicatorPolicy());
        }
    }

    public function onFetch(DatabaseFetchEvent $event)
    {
        $model = $event->getModel();
        $policyCollection = $model->getPolicyCollection();

        // TODO determine is primary (no TODO), backup server (no TODO) or stub
        // TODO if it is stub: load data from random server (out of primary and backups)
    }

    public function storeStub($hash, ServerInterface $primaryServer, array $backupServers)
    {
    }

    public function store($hash, $data)
    {
    }

    public function fetch($hash)
    {
    }
}
