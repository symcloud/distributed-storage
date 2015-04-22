<?php

namespace Symcloud\Component\MetadataStorage\Model;

class TreeModel implements TreeInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var ObjectModel
     */
    private $children;

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return ObjectModel
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ObjectModel $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }
}
