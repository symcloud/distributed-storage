<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Access;

use Symcloud\Component\Access\Exception\NotAFileException;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileManager implements FileManagerInterface
{
    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * FileManager constructor.
     *
     * @param ReferenceManagerInterface $referenceManager
     * @param TreeManagerInterface      $treeManager
     * @param FactoryInterface          $factory
     */
    public function __construct(
        ReferenceManagerInterface $referenceManager,
        TreeManagerInterface $treeManager,
        FactoryInterface $factory
    ) {
        $this->referenceManager = $referenceManager;
        $this->treeManager = $treeManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getByPath($path, UserInterface $user)
    {
        $reference = $this->referenceManager->getForUser($user);
        $commit = $reference->getCommit();
        $tree = $commit->getTree();

        $treeWalker = $this->treeManager->getTreeWalker($tree);
        $node = $treeWalker->walk($path);

        // TODO security-checker for object

        if (!$node->isFile()) {
            throw new NotAFileException($path);
        }

        return $this->factory->createFile($node);
    }
}
