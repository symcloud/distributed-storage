<?php

namespace Symcloud\Component\MetadataStorage\Model;

interface MetadataInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return KeyValueInterface
     */
    public function getKeyValueStore();
}
