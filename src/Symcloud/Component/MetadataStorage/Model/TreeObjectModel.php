<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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

    /**
     * @return bool
     */
    public function isFile()
    {
        return false;
    }
}
