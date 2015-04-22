<?php

namespace Symcloud\Component\MetadataStorage\Model;

class CommitModel implements CommitInterface
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
     * @var CommitModel
     */
    private $parentCommit;

    /**
     * @var TreeModel
     */
    private $tree;

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
     * @return CommitModel
     */
    public function getParentCommit()
    {
        return $this->parentCommit;
    }

    /**
     * @param CommitModel $parentCommit
     */
    public function setParentCommit($parentCommit)
    {
        $this->parentCommit = $parentCommit;
    }

    /**
     * @return TreeModel
     */
    public function getTree()
    {
        return $this->tree;
    }

    /**
     * @param TreeModel $tree
     */
    public function setTree($tree)
    {
        $this->tree = $tree;
    }
}
