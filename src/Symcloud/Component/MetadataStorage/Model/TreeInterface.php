<?php
namespace Symcloud\Component\MetadataStorage\Model;

interface TreeInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return ObjectModel
     */
    public function getChildren();
}
