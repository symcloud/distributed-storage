<?php
namespace Symcloud\Component\MetadataStorage\Model;

interface CommitInterface
{
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
     * @return TreeModel
     */
    public function getTree();
}
