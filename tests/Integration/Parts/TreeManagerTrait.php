<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Tree\TreeManager;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;

trait TreeManagerTrait
{
    use BlobFileManagerTrait;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    public function getTreeManager()
    {
        if (!$this->treeManager) {
            $this->treeManager = new TreeManager(
                $this->getDatabase(),
                $this->getBlobFileManager(),
                $this->getUserProvider(),
                $this->getFactory()
            );
        }

        return $this->treeManager;
    }
}
