<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

trait CommitManagerTrait
{
    use SerializeAdapterTrait;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    public function getCommitManager()
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

    public function getCommitAdapter()
    {
        return $this->getSerializeAdapter();
    }

    public function getUserProvider()
    {
        if (!$this->userProvider) {
            $this->userProvider = $this->createUserProvider();
        }

        return $this->userProvider;
    }

    public abstract function createUserProvider();

    public abstract function getTreeManager();
}
