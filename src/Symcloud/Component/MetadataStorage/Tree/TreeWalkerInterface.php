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

use Symcloud\Component\Database\Model\Tree\TreeNodeInterface;

interface TreeWalkerInterface
{
    /**
     * @param string $path
     *
     * @return TreeNodeInterface
     */
    public function walk($path);
}
