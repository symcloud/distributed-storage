<?php

namespace Symcloud\Component\MetadataStorage\Tree;

use Symcloud\Component\MetadataStorage\Model\ObjectInterface;

interface TreeWalkerInterface
{
    /**
     * @param $path
     * @return ObjectInterface
     */
    public function walk($path);
}
