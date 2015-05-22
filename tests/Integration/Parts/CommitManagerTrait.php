<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;

trait CommitManagerTrait
{
    use TreeManagerTrait;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    protected function getCommitManager()
    {
        if (!$this->commitManager) {
            $this->commitManager = new CommitManager(
                $this->getFactory(),
                $this->getDatabase(),
                $this->getUserProvider(),
                $this->getTreeManager()
            );
        }

        return $this->commitManager;
    }
}
