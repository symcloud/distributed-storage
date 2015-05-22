<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model\Reference;

use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceInterface extends ModelInterface
{
    /**
     * @return CommitInterface
     */
    public function getCommit();

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param CommitInterface $commit
     */
    public function update(CommitInterface $commit);
}
