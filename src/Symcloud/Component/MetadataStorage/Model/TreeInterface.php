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
     *
     * @return NodeInterface
     */
    public function getChild($name);

    /**
     * @param string $name
     * @param NodeInterface $node
     */
    public function setChild($name, NodeInterface $node);

    /**
     * @return bool
     */
    public function isRoot();
}
