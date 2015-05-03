<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

interface TreeInterface extends NodeInterface
{
    const CHILDREN_KEY = 'children';

    /**
     * @return NodeInterface[]
     */
    public function getChildren();

    /**
     * @param string $name
     * @param NodeInterface $node
     */
    public function setChild($name, NodeInterface $node);

    /**
     * @return boolean
     */
    public function isRoot();
}
