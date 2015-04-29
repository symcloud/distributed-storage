<?php

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class TreeManager implements TreeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTreeWalker(TreeInterface $tree)
    {
        // TODO: Implement getTreeWalker() method.
    }

    /**
     * @param string $hash
     * @return TreeInterface
     */
    public function fetch($hash)
    {
        // TODO: Implement fetch() method.
    }

    /**
     * @param string $hash
     * @return TreeInterface
     */
    public function fetchProxy($hash)
    {
        // TODO: Implement fetchProxy() method.
    }
}
