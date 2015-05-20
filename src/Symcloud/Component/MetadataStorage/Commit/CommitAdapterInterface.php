<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Commit;

use Symcloud\Component\Common\AdapterInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;

interface CommitAdapterInterface extends AdapterInterface
{
    /**
     * @param CommitInterface $commit
     */
    public function storeCommit(CommitInterface $commit);

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchCommitData($hash);
}
