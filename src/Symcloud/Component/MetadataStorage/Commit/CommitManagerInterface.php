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

use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CommitManagerInterface
{
    /**
     * @param TreeInterface   $tree
     * @param UserInterface   $user
     * @param string          $message
     * @param CommitInterface $parentCommit
     *
     * @return CommitInterface
     */
    public function commit(
        TreeInterface $tree,
        UserInterface $user,
        $message = '',
        CommitInterface $parentCommit = null
    );

    /**
     * @param string $hash
     *
     * @return CommitInterface
     */
    public function fetch($hash);

    /**
     * @param string $hash
     *
     * @return CommitInterface
     */
    public function fetchProxy($hash);
}
