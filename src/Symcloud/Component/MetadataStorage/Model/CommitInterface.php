<?php
namespace Symcloud\Component\MetadataStorage\Model;

use Symfony\Component\Security\Core\User\UserInterface;

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

    /**
     * @return UserInterface
     */
    public function getCommitter();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
