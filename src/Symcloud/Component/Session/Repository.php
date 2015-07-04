<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Session;

use Symcloud\Component\FileStorage\ChunkFileManagerInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Repository implements RepositoryInterface
{
    /**
     * @var ChunkFileManagerInterface
     */
    private $chunkFileManager;

    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * Repository constructor.
     *
     * @param ChunkFileManagerInterface $chunkFileManager
     * @param ReferenceManagerInterface $referenceManager
     * @param TreeManagerInterface $treeManager
     * @param CommitManagerInterface $commitManager
     */
    public function __construct(
        ChunkFileManagerInterface $chunkFileManager,
        ReferenceManagerInterface $referenceManager,
        TreeManagerInterface $treeManager,
        CommitManagerInterface $commitManager
    ) {
        $this->chunkFileManager = $chunkFileManager;
        $this->referenceManager = $referenceManager;
        $this->treeManager = $treeManager;
        $this->commitManager = $commitManager;
    }

    /**
     * {@inheritdoc}
     */
    public function loginByName(UserInterface $user, $name)
    {
        $hash = $this->referenceManager->createHash($user, $name);

        return new Session(
            $this->chunkFileManager,
            $this->referenceManager,
            $this->treeManager,
            $this->commitManager,
            $user,
            $name,
            $hash
        );
    }

    /**
     * {@inheritdoc}
     */
    public function loginByHash(UserInterface $user, $hash)
    {
        return new Session(
            $this->chunkFileManager,
            $this->referenceManager,
            $this->treeManager,
            $this->commitManager,
            $user,
            null,
            $hash
        );
    }
}
