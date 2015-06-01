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

interface TreeInterface extends TreeNodeInterface
{
    /**
     * @return TreeNodeInterface[]
     */
    public function getChildren();

    /**
     * @param string $name
     *
     * @return TreeNodeInterface
     */
    public function getChild($name);

    /**
     * @param string $name
     * @param TreeNodeInterface $node
     */
    public function setChild($name, TreeNodeInterface $node);

    /**
     * @param string $name
     */
    public function removeChild($name);
}
