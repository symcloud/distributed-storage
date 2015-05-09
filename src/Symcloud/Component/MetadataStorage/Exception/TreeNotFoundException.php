<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Exception;

use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class TreeNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var TreeInterface
     */
    private $root;

    /**
     * NotATreeException constructor.
     *
     * @param TreeInterface $root
     * @param string $path
     */
    public function __construct($root, $path)
    {
        parent::__construct(sprintf('Node with path "%s" in tree was not found', $path, $root->getHash()));

        $this->root = $root;
        $this->path = $path;
    }

    /**
     * @return TreeInterface
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
