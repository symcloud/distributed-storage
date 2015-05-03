<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class MaterializedPathTreeWalker implements TreeWalkerInterface
{
    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * MaterializedPathTreeWalker constructor.
     *
     * @param TreeInterface $tree
     * @param TreeManagerInterface $treeManager
     */
    public function __construct(TreeInterface $tree, TreeManagerInterface $treeManager)
    {
        $this->treeManager = $treeManager;
        $this->tree = $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function walk($path)
    {
        // TODO implement walk()
        // FAIL here is that complete content has to be hashed
        // $absolutePath = sprintf('/%s/%s', ltrim($this->tree->getPath()), ltrim($path, '/'));
        // $hash = $this->treeManager->createHash($absolutePath, $this->tree->getRoot()->getHash());
        // return $this->treeManager->fetchProxy($hash);
    }
}
