<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model\Commit;

use Symcloud\Component\Database\Model\DistributedModelInterface;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CommitInterface extends DistributedModelInterface
{
    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return CommitInterface
     */
    public function getParentCommit();

    /**
     * @return TreeInterface
     */
    public function getTree();

    /**
     * @return UserInterface
     */
    public function getCommitter();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
