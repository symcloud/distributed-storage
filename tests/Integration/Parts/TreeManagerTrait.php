<?php

namespace Integration\Parts;

use Symcloud\Component\MetadataStorage\Tree\TreeManager;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;

trait TreeManagerTrait
{
    use BlobFileManagerTrait, MetadataAdapterTrait;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    public function getTreeManager()
    {
        if (!$this->treeManager) {
            $this->treeManager = new TreeManager(
                $this->getTreeAdapter(),
                $this->getBlobFileManager(),
                $this->getFactory()
            );
        }

        return $this->treeManager;
    }

    public function getTreeAdapter()
    {
        return $this->getSerializeAdapter();
    }
}
