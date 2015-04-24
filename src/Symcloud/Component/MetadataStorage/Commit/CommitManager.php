<?php

namespace Symcloud\Component\MetadataStorage\Commit;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommitManager implements CommitManagerInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var CommitAdapterInterface
     */
    private $commitAdapter;

    /**
     * CommitManager constructor.
     * @param FactoryInterface $factory
     * @param CommitAdapterInterface $commitAdapter
     */
    public function __construct(FactoryInterface $factory, CommitAdapterInterface $commitAdapter)
    {
        $this->factory = $factory;
        $this->commitAdapter = $commitAdapter;
    }

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
    ) {
        $commit = $this->factory->createCommit($tree, $user, $message, $parentCommit);
        $this->commitAdapter->storeCommit($commit);

        return $commit;
    }

    /**
     * @param string $hash
     * @return CommitInterface
     */
    public function fetch($hash)
    {
        return $this->commitAdapter->fetchCommit($hash);
    }
}
