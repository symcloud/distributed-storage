<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface CommitInterface
{
    const TREE_KEY = 'tree';
    const MESSAGE_KEY = 'message';
    const PARENT_COMMIT_KEY = 'parentCommit';
    const COMMITTER_KEY = 'committer';
    const CREATED_AT_KEY = 'createdAt';

    /**
     * @return string
     */
    public function getHash();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @return CommitModel
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

    /**
     * @return array
     */
    public function toArray();
}
