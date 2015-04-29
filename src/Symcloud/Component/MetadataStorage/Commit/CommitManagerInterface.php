<?php

namespace Symcloud\Component\MetadataStorage\Commit;

use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CommitManagerInterface
{

    /**
     * @param TreeInterface $tree
     * @param UserInterface $user
     * @param string $message
     * @param CommitInterface $parentCommit
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
     * @return CommitInterface
     */
    public function fetch($hash);

    /**
     * @param string $hash
     * @return CommitInterface
     */
    public function fetchProxy($hash);
}
