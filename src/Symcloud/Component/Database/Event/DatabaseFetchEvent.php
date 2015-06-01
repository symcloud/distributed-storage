<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Event;

use Symcloud\Component\Database\Metadata\ClassMetadataInterface;

class DatabaseFetchEvent extends DatabaseEvent
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $class;

    /**
     * @var ClassMetadataInterface
     */
    private $metadata;

    /**
     * DatabaseFetchEvent constructor.
     *
     * @param string $hash
     * @param array $data
     * @param string $class
     * @param ClassMetadataInterface $metadata
     */
    public function __construct($hash, $data, $class, ClassMetadataInterface $metadata)
    {
        $this->hash = $hash;
        $this->data = $data;
        $this->class = $class;
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return ClassMetadataInterface
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
