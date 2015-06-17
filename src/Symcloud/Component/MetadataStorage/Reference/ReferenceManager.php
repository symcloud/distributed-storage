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
use Symcloud\Component\Database\Model\Reference\Reference;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\Database\Search\Hit\HitInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ReferenceManager implements ReferenceManagerInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var
     */
    private $hostName;

    /**
     * ReferenceManager constructor.
     *
     * @param DatabaseInterface $database
     * @param UserProviderInterface $userProvider
     * @param FactoryInterface $factory
     * @param $hostName
     */
    public function __construct(
        DatabaseInterface $database,
        UserProviderInterface $userProvider,
        FactoryInterface $factory,
        $hostName
    ) {
        $this->database = $database;
        $this->factory = $factory;
        $this->userProvider = $userProvider;
        $this->hostName = $hostName;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, UserInterface $user, CommitInterface $commit)
    {
        $reference = new Reference();
        $reference->setHash($this->createHash($user, $name));
        $reference->setName($name);
        $reference->setUser($user);
        $reference->setCommit($commit);

        return $this->database->store($reference);
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
    public function fetch($hash)
    {
        return $this->database->fetch($hash, Reference::class);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(UserInterface $user)
    {
        $hits = $this->database->search(sprintf('user:%s', $user->getUsername()), array('reference'));

        return array_map(
            function (HitInterface $hit) {
                return $this->database->fetch($hit->getHash(), Reference::class);
            },
            $hits
        );
    }

    /**
     * {@inheritdoc}
     */
    public function createHash(UserInterface $user, $name)
    {
        return $this->factory->createHash(sprintf('%s@%s/%s', $user->getUsername(), $this->hostName, $name));
    }
}
