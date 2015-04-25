<?php

namespace Symcloud\Component\MetadataStorage\Commit;

use Symcloud\Component\MetadataStorage\Model\CommitInterface;

interface CommitAdapterInterface
{
    /**
     * @param CommitInterface $commit
     * @return boolean
     */
    public function storeCommit(CommitInterface $commit);

    /**
     * @param string $hash
     * @return array
     */
    public function fetchCommit($hash);
}
