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
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferenceManager implements ReferenceManagerInterface
{
    /**
     * @var ReferenceAdapterInterface
     */
    private $adapter;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * ReferenceManager constructor.
     *
     * @param ReferenceAdapterInterface $adapter
     * @param CommitManagerInterface    $commitManager
     * @param FactoryInterface          $factory
     */
    public function __construct(
        ReferenceAdapterInterface $adapter,
        CommitManagerInterface $commitManager,
        FactoryInterface $factory
    ) {
        $this->adapter = $adapter;
        $this->commitManager = $commitManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(UserInterface $user, CommitInterface $commit, $name = 'HEAD')
    {
        $reference = $this->factory->createReference($commit, $user, $name);
        $this->adapter->storeReference($reference);

        return $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ReferenceInterface $reference, CommitInterface $commit)
    {
        $reference->update($commit);

        return $this->adapter->storeReference($reference);
    }

    /**
     * {@inheritdoc}
     */
    public function getForUser(UserInterface $user, $name = 'HEAD')
    {
        $referenceData = $this->adapter->fetchReferenceData($user, $name);
        $commit = $this->commitManager->fetchProxy($referenceData[ReferenceInterface::COMMIT_KEY]);

        return $this->factory->createReference($commit, $user, $referenceData[ReferenceInterface::NAME_KEY]);
    }
}
