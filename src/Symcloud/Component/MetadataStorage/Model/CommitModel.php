<?php

namespace Symcloud\Component\MetadataStorage\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class CommitModel implements CommitInterface, \JsonSerializable
{
    /**
     * @var string
     */
    private $hash;

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
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

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
    public function setParentCommit(CommitInterface $parentCommit)
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
     * @return array
     */
    public function toArray()
    {
        return array(
            'tree' => $this->getTree()->getHash(),
            'message' => $this->getMessage(),
            'parentCommit' => ($this->getParentCommit() !== null ? $this->getParentCommit()->getHash() : null),
            'committer' => $this->getCommitter()->getUsername(),
            'createdAt' => $this->getCreatedAt()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
