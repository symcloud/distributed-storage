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
use Symcloud\Component\MetadataStorage\Model\TreeReferenceInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;

class SequentialTreeWalker implements TreeWalkerInterface
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
     * SequentialTreeWalker constructor.
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
    public function walk($path, ReferenceManagerInterface $referenceManager = null)
    {
        $parts = array_filter(explode('/', $path));
        $tree = $this->tree;
        foreach ($parts as $part) {
            $tree = $tree->getChild($part);

            if ($tree instanceof TreeReferenceInterface) {
                if (!$referenceManager) {
                    throw new \Exception('unresolved reference detected');
                }

                $reference = $referenceManager->getForUser($tree->getUser(), $tree->getReferenceName());
                $tree = $reference->getCommit()->getTree();
            }

            if ($tree === null) {
                return;
            }
        }

        return $tree;
    }
}
