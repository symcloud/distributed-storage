<?php

namespace Symcloud\Component\MetadataStorage\Model;

class ReferenceModel implements ReferenceInterface
{
    // FIXME here should a commit be

    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * {@inheritdoc}
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param ReferenceModel $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }
}
