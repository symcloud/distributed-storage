<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Reference\ReferenceManager;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;

trait ReferenceManagerTrait
{
    use CommitManagerTrait;

    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    protected function getReferenceManager()
    {
        if (!$this->referenceManager) {
            $this->referenceManager = new ReferenceManager(
                $this->getReferenceAdapter(),
                $this->getCommitManager(),
                $this->getFactory()
            );
        }

        return $this->referenceManager;
    }

    protected function getReferenceAdapter()
    {
        return $this->getSerializeAdapter();
    }
}
