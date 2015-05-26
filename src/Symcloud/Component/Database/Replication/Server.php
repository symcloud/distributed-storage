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

class Server implements ServerInterface
{
    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * Server constructor.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port = 80)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($path)
    {
        // TODO scheme
        return sprintf(
            'http://%s:%s/%s',
            $this->getHost(),
            $this->getPort(),
            ltrim($path, '/')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            array('host' => $this->host, 'port' => $this->port)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->host = $data['host'];
        $this->port = $data['port'];
    }
}
