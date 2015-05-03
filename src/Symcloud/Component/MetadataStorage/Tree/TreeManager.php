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

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Exception\NotAFileException;
use Symcloud\Component\MetadataStorage\Exception\NotATreeException;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;

class TreeManager implements TreeManagerInterface
{
    /**
     * @var TreeAdapterInterface
     */
    private $treeAdapter;

    /**
     * @var BlobFileManagerInterface
     */
    private $blobFileManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * {@inheritdoc}
     */
    public function getTreeWalker(TreeInterface $tree)
    {
        return new MaterializedPathTreeWalker($tree, $this);
    }

    /**
     * {@inheritdoc}
     */
    public function createRootTree()
    {
        return $this->factory->createRootTree();
    }

    /**
     * {@inheritdoc}
     */
    public function createTree($name, TreeInterface $parent)
    {
        $tree = $this->factory->createTree(sprintf('%s/%s', $parent->getPath(), $name), $parent->getRoot());
        $parent->setChild($name, $tree);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createTreeFile($name, TreeInterface $parent, BlobFileInterface $blobFile, $metadata = array())
    {
        $file = $this->factory->createTreeFile(
            sprintf('%s/%s', $parent->getPath(), $name),
            $name,
            $parent->getRoot(),
            $blobFile,
            $metadata
        );
        $parent->setChild($name, $file);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($path, $rootHash)
    {
        return $this->factory->createHash(
            json_encode(
                array(
                    NodeInterface::PATH_KEY => $path,
                    NodeInterface::ROOT_KEY => $rootHash,
                )
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function store(TreeInterface $tree)
    {
        foreach ($tree->getChildren() as $child) {
            if ($child instanceof TreeInterface) {
                $this->store($child);
            }

            $this->storeFile($child);
        }

        $this->storeTree($tree);
    }

    private function storeFile(TreeFileInterface $child)
    {
        $child->setHash($this->factory->createHash(json_encode($child)));
        $this->storeNode($child);
    }

    private function storeTree(TreeInterface $child)
    {
        $child->setHash($this->createHash($child->getPath(), $child->getRoot()));
        $this->storeNode($child);
    }

    /**
     * @param NodeInterface $child
     */
    private function storeNode(NodeInterface $child)
    {
        $this->treeAdapter->storeTree($child);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        $data = $this->treeAdapter->fetchTreeData($hash);

        $path = $data[NodeInterface::PATH_KEY];
        $type = $data[TreeInterface::TYPE_KEY];
        if ($type !== NodeInterface::TREE_TYPE) {
            throw new NotATreeException($hash, $path);
        }

        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);

        $children = array();
        foreach ($data[TreeInterface::CHILDREN_KEY][TreeInterface::TREE_TYPE] as $childHash) {
            $children[] = $this->fetchProxy($childHash);
        }
        foreach ($data[TreeInterface::CHILDREN_KEY][TreeInterface::FILE_TYPE] as $childHash) {
            $children[] = $this->fetchFileProxy($childHash);
        }

        return $this->factory->createTree($path, $root, $children, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        $data = $this->treeAdapter->fetchTreeData($hash);

        $path = $data[NodeInterface::PATH_KEY];
        $type = $data[TreeInterface::TYPE_KEY];
        if ($type !== NodeInterface::FILE_TYPE) {
            throw new NotAFileException($hash, $path);
        }

        $name = basename($path);
        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);
        $blobFile = $this->blobFileManager->downloadProxy($data[TreeFileInterface::FILE_KEY]);
        $metadata = $data[TreeFileInterface::METADATA_KEY];

        return $this->factory->createTreeFile($path, $name, $root, $blobFile, $metadata, $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchProxy($hash)
    {
        return $this->factory->createProxy(
            TreeInterface::class,
            function () use ($hash) {
                return $this->fetch($hash);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFileProxy($hash)
    {
        return $this->factory->createProxy(
            TreeInterface::class,
            function () use ($hash) {
                return $this->fetchFile($hash);
            }
        );
    }
}
