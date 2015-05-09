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
     * TreeManager constructor.
     *
     * @param TreeAdapterInterface $treeAdapter
     * @param BlobFileManagerInterface $blobFileManager
     * @param FactoryInterface $factory
     */
    public function __construct(
        TreeAdapterInterface $treeAdapter,
        BlobFileManagerInterface $blobFileManager,
        FactoryInterface $factory
    ) {
        $this->treeAdapter = $treeAdapter;
        $this->blobFileManager = $blobFileManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getTreeWalker(TreeInterface $tree)
    {
        return new SequentialTreeWalker($tree, $this);
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
        $tree = $this->factory->createTree(sprintf('%s/%s', $parent->getPath(), $name), $parent->getRoot(), $parent);
        $parent->setChild($name, $tree);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createTreeFile($name, TreeInterface $parent, BlobFileInterface $blobFile, $metadata = array())
    {
        $file = $this->factory->createTreeFile(
            ltrim(sprintf('%s/%s', $parent->getPath(), $name), '/'),
            $name,
            $parent->getRoot(),
            $parent,
            $blobFile,
            $metadata
        );
        $parent->setChild($name, $file);

        return $file;
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

            $this->storeNode($child);
        }

        $this->storeNode($tree);
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

        return $this->parseTreeData($hash, $data);
    }

    /**
     * @param array $children
     *
     * @return array
     */
    private function deserializeChildren($children)
    {
        $result = array();
        foreach ($children[TreeInterface::TREE_TYPE] as $childHash) {
            $result[] = $this->fetchProxy($childHash);
        }
        foreach ($children[TreeInterface::FILE_TYPE] as $childHash) {
            $result[] = $this->fetchFileProxy($childHash);
        }

        return $result;
    }

    /**
     * @param string $hash
     * @param array $data
     *
     * @return TreeInterface
     *
     * @throws NotATreeException
     */
    private function parseTreeData($hash, $data)
    {
        $path = $data[NodeInterface::PATH_KEY];
        $type = $data[TreeInterface::TYPE_KEY];
        if ($type !== NodeInterface::TREE_TYPE) {
            throw new NotATreeException($hash, $path);
        }

        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);
        $children = $this->deserializeChildren($data[TreeInterface::CHILDREN_KEY]);

        return $this->factory->createTree($path, $root, $this->fetchByPathProxy(dirname($path), $root), $children);
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
        $parent = $this->fetchProxy($data[NodeInterface::PARENT_KEY]);

        return $this->factory->createTreeFile($path, $name, $root, $parent, $blobFile, $metadata);
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

    public function fetchByPathProxy($absolutePath, $root)
    {
        return $this->factory->createProxy(
            TreeInterface::class,
            function () use ($absolutePath, $root) {
                return $this->fetchByPath($absolutePath, $root);
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
