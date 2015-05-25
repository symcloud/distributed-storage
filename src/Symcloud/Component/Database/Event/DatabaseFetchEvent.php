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
     * @var array
     */
    private $data;

    /**
     * @var ClassMetadataInterface
     */
    private $metadata;

    /**
     * DatabaseFetchEvent constructor.
     *
     * @param array $data
     * @param ClassMetadataInterface $metadata
     */
    public function __construct(array $data, ClassMetadataInterface $metadata)
    {
        $this->data = $data;
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
}
