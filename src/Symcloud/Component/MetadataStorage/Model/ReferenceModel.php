<?php

namespace Symcloud\Component\MetadataStorage\Model;

class ReferenceModel implements ReferenceInterface
{
    /**
     * @var CommitInterface
     */
    private $commit;

    /**
     * {@inheritdoc}
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param CommitInterface $commit
     */
    public function setCommit(CommitInterface $commit)
    {
        $this->commit = $commit;
    }
}
