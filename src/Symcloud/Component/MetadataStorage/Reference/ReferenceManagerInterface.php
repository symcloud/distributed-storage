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

use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceManagerInterface
{
    /**
     * @param UserInterface $user
     * @param string        $name
     *
     * @return ReferenceInterface
     */
    public function getForUser(UserInterface $user, $name = 'HEAD');

    /**
     * @param UserInterface   $user
     * @param CommitInterface $commit
     * @param string          $name
     *
     * @return ReferenceInterface
     */
    public function create(UserInterface $user, CommitInterface $commit, $name = 'HEAD');

    /**
     * @param ReferenceInterface $reference
     * @param CommitInterface    $commit
     *
     * @return bool
     */
    public function update(ReferenceInterface $reference, CommitInterface $commit);
}
