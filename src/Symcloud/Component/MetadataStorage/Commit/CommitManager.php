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
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
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
     * CommitManager constructor.
     *
     * @param FactoryInterface       $factory
     * @param DatabaseInterface $database
     * @param UserProviderInterface  $userProvider
     */
    public function __construct(
        FactoryInterface $factory,
        DatabaseInterface $database,
        UserProviderInterface $userProvider
    ) {
        $this->factory = $factory;
        $this->database = $database;
        $this->userProvider = $userProvider;
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
        $commit->setPolicyCollection(new PolicyCollection());
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
}
