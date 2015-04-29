<?php

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\MetadataStorage\Model\TreeInterface;

interface TreeManagerInterface
{
    /**
     * @param TreeInterface $tree
     * @return TreeWalkerInterface
     */
    public function getTreeWalker(TreeInterface $tree);

    /**
     * @param string $hash
     * @return TreeInterface
     */
    public function fetch($hash);

    /**
     * @param string $hash
     * @return TreeInterface
     */
    public function fetchProxy($hash);
}
