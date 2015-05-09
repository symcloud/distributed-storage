<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\MetadataStorage\Model\NodeInterface;

interface TreeAdapterInterface
{
    const HASH_KEY = 'hash';

    /**
     * @param NodeInterface $tree
     */
    public function storeTree(NodeInterface $tree);

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchTreeData($hash);
}
