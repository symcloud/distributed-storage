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

use Symcloud\Component\Database\Model\PolicyInterface;

class ReplicatorPolicy implements PolicyInterface
{
    /**
     * @var ServerInterface
     */
    private $primaryServer;

    /**
     * @var ServerInterface[]
     */
    private $backupServers = array();

    /**
     * ReplicatorPolicy constructor.
     *
     * @param ServerInterface $primaryServer
     * @param ServerInterface[] $backupServers
     */
    public function __construct(ServerInterface $primaryServer = null, array $backupServers = array())
    {
        $this->primaryServer = $primaryServer;
        $this->backupServers = $backupServers;
    }

    /**
     * @return ServerInterface
     */
    public function getPrimaryServer()
    {
        return $this->primaryServer;
    }

    /**
     * @param ServerInterface $primaryServer
     */
    public function setPrimaryServer($primaryServer)
    {
        $this->primaryServer = $primaryServer;
    }

    /**
     * @return ServerInterface[]
     */
    public function getBackupServers()
    {
        return $this->backupServers;
    }

    /**
     * @param ServerInterface[] $backupServers
     */
    public function setBackupServers($backupServers)
    {
        $this->backupServers = $backupServers;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $result = array(
            'primary' => serialize($this->primaryServer),
            'backups' => array(),
        );

        foreach ($this->backupServers as $backup) {
            $result['backups'][] = serialize($backup);
        }

        return serialize($result);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->primaryServer = unserialize($data['primary']);

        $this->backupServers = array();
        foreach ($data['backups'] as $backup) {
            $this->backupServers[] = unserialize($backup);
        }
    }
}
