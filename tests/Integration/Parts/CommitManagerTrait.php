<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

trait CommitManagerTrait
{
    use MetadataAdapterTrait, TreeManagerTrait;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    protected function getCommitManager()
    {
        if (!$this->commitManager) {
            $this->commitManager = new CommitManager(
                $this->getFactory(),
                $this->getCommitAdapter(),
                $this->getUserProvider(),
                $this->getTreeManager()
            );
        }

        return $this->commitManager;
    }

    protected function getCommitAdapter()
    {
        return $this->getSerializeAdapter();
    }

    protected function getUserProvider()
    {
        if (!$this->userProvider) {
            $this->userProvider = $this->createUserProvider();
        }

        return $this->userProvider;
    }

    protected abstract function createUserProvider();
}
