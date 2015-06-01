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

use Symcloud\Component\Database\Model\DistributedModel;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Commit extends DistributedModel implements CommitInterface
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var CommitInterface
     */
    private $parentCommit;

    /**
     * @var TreeInterface
     */
    private $tree;

    /**
     * @var UserInterface
     */
    private $committer;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return CommitInterface
     */
    public function getParentCommit()
    {
        return $this->parentCommit;
    }

    /**
     * @param CommitInterface $parentCommit
     */
    public function setParentCommit(CommitInterface $parentCommit = null)
    {
        $this->parentCommit = $parentCommit;
    }

    /**
     * @return TreeInterface
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param TreeInterface $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }

    /**
     * @return UserInterface
     */
    public function getCommitter()
    {
        return $this->committer;
    }

    /**
     * @param UserInterface $committer
     */
    public function setCommitter($committer)
    {
        $this->committer = $committer;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }
}
