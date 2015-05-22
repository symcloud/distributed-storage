<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Model\Reference\Reference;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ReferenceManager implements ReferenceManagerInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * ReferenceManager constructor.
     *
     * @param DatabaseInterface $database
     * @param CommitManagerInterface $commitManager
     * @param UserProviderInterface $userProvider
     * @param FactoryInterface $factory
     */
    public function __construct(
        DatabaseInterface $database,
        CommitManagerInterface $commitManager,
        UserProviderInterface $userProvider,
        FactoryInterface $factory
    ) {
        $this->database = $database;
        $this->commitManager = $commitManager;
        $this->factory = $factory;
        $this->userProvider = $userProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, UserInterface $user, CommitInterface $commit)
    {
        $reference = new Reference();
        $reference->setPolicy(new Policy());
        $reference->setName($name);
        $reference->setUser($user);
        $reference->setCommit($commit);
        $this->database->store($reference);

        return $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ReferenceInterface $reference, CommitInterface $commit)
    {
        $reference->update($commit);

        return $this->database->store($reference);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($name)
    {
        return $this->database->fetch($name);
    }
}
