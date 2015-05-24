<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model\Tree;

use Symcloud\Component\Database\Model\DistributedModelInterface;

interface TreeNodeInterface extends DistributedModelInterface
{
    const TREE_TYPE = 'tree';
    const FILE_TYPE = 'file';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return bool
     */
    public function getIsFile();
}
