<?php

namespace Symcloud\Component\MetadataStorage\Model;

interface NodeInterface extends \JsonSerializable
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return TreeInterface
     */
    public function getRoot();
}
