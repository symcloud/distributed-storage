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

use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceManagerInterface
{
    /**
     * @param string        $name
     *
     * @return ReferenceInterface
     */
    public function fetch($name);

    /**
     * @param string $name
     * @param UserInterface   $user
     * @param CommitInterface $commit
     *
     * @return ReferenceInterface
     */
    public function create($name, UserInterface $user, CommitInterface $commit);

    /**
     * @param ReferenceInterface $reference
     * @param CommitInterface    $commit
     *
     * @return bool
     */
    public function update(ReferenceInterface $reference, CommitInterface $commit);
}
