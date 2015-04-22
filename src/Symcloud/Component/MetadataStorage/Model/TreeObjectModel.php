<?php

namespace Symcloud\Component\MetadataStorage\Model;

class TreeObjectModel extends ObjectModel
{
    /**
     * @var TreeModel
     */
    private $tree;

    /**
     * @return TreeModel
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param TreeModel $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }
}
