<?php
namespace Symcloud\Component\MetadataStorage\Model;

interface ReferenceInterface
{
    /**
     * @return TreeInterface
     */
    public function getTree();
}
