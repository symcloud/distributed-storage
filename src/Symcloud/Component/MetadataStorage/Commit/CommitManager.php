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
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
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
     * @var DatabaseInterface
     */
    private $database;

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
     * @param DatabaseInterface $database
     * @param UserProviderInterface  $userProvider
     * @param TreeManagerInterface   $treeManager
     */
    public function __construct(
        FactoryInterface $factory,
        DatabaseInterface $database,
        UserProviderInterface $userProvider,
        TreeManagerInterface $treeManager
    ) {
        $this->factory = $factory;
        $this->database = $database;
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
        $commit = new Commit();
        $commit->setPolicy(new Policy());
        $commit->setCommitter($user);
        $commit->setParentCommit($parentCommit);
        $commit->setCreatedAt(new \DateTime());
        $commit->setMessage($message);
        $commit->setTree($tree);

        return $this->database->store($commit);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        return $this->database->fetch($hash, Commit::class);
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
