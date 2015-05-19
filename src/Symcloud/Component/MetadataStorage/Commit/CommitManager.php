<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Commit;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CommitManager implements CommitManagerInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var CommitAdapterInterface
     */
    private $commitAdapter;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * CommitManager constructor.
     *
     * @param FactoryInterface       $factory
     * @param CommitAdapterInterface $commitAdapter
     * @param UserProviderInterface  $userProvider
     * @param TreeManagerInterface   $treeManager
     */
    public function __construct(
        FactoryInterface $factory,
        CommitAdapterInterface $commitAdapter,
        UserProviderInterface $userProvider,
        TreeManagerInterface $treeManager
    ) {
        $this->factory = $factory;
        $this->commitAdapter = $commitAdapter;
        $this->userProvider = $userProvider;
        $this->treeManager = $treeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function commit(
        TreeInterface $tree,
        UserInterface $user,
        $message = '',
        CommitInterface $parentCommit = null
    ) {
        $commit = $this->factory->createCommit($tree, $user, new \DateTime(), $message, $parentCommit);
        $this->commitAdapter->storeCommit($commit);

        return $commit;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        $data = $this->commitAdapter->fetchCommitData($hash);

        $message = $data[CommitInterface::MESSAGE_KEY];
        $createdAt = \DateTime::createFromFormat(\DateTime::ISO8601, $data[CommitInterface::CREATED_AT_KEY]);

        $user = $this->factory->createProxy(
            UserInterface::class,
            function () use ($data) {
                return $this->userProvider->loadUserByUsername($data[CommitInterface::COMMITTER_KEY]);
            }
        );

        $tree = $this->treeManager->fetchProxy($data[CommitInterface::TREE_KEY]);

        $parentCommit = null;
        if ($data[CommitInterface::PARENT_COMMIT_KEY] !== null) {
            $parentCommit = $this->fetchProxy($data[CommitInterface::PARENT_COMMIT_KEY]);
        }

        return $this->factory->createCommit($tree, $user, $createdAt, $message, $parentCommit, $hash);
    }

    /**
     * @param string $hash
     *
     * @return CommitInterface
     */
    public function fetchProxy($hash)
    {
        return $this->factory->createProxy(
            CommitInterface::class,
            function () use ($hash) {
                return $this->fetch($hash);
            }
        );
    }
}
