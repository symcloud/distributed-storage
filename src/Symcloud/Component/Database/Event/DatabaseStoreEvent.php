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
use Symcloud\Component\Database\Model\ModelInterface;

class DatabaseStoreEvent extends DatabaseEvent
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $data;

    /**
     * @var ClassMetadataInterface
     */
    private $metadata;

    /**
     * @var bool
     */
    private $isNew;

    /**
     * DatabaseStoreEvent constructor.
     *
     * @param ModelInterface $model
     * @param array $data
     * @param bool $isNew
     * @param ClassMetadataInterface $metadata
     * @param array $options
     */
    public function __construct(
        ModelInterface $model,
        array $data,
        $isNew,
        ClassMetadataInterface $metadata,
        array $options
    ) {
        parent::__construct($model);

        $this->options = $options;
        $this->data = $data;
        $this->metadata = $metadata;
        $this->isNew = $isNew;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $name
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (!array_key_exists($name, $this->options)) {
            return $default;
        }

        return $this->options[$name];
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
     * @return bool
     */
    public function isNew()
    {
        return $this->isNew;
    }

    /**
     * @return array
     */
    public function getMetadataObject()
    {
    }
}
