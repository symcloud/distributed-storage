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

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Exception\NotAFileException;
use Symcloud\Component\MetadataStorage\Exception\NotATreeException;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeReferenceInterface;
use Symcloud\Component\Session\Exception\NotAReferenceException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

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
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * TreeManager constructor.
     *
     * @param TreeAdapterInterface $treeAdapter
     * @param BlobFileManagerInterface $blobFileManager
     * @param UserProviderInterface $userProvider
     * @param FactoryInterface $factory
     */
    public function __construct(
        TreeAdapterInterface $treeAdapter,
        BlobFileManagerInterface $blobFileManager,
        UserProviderInterface $userProvider,
        FactoryInterface $factory
    ) {
        $this->treeAdapter = $treeAdapter;
        $this->blobFileManager = $blobFileManager;
        $this->userProvider = $userProvider;
        $this->factory = $factory;

        $this->cache = new ArrayCache();
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
            '/' . ltrim(sprintf('%s/%s', $parent->getPath(), $name), '/'),
            $name,
            $parent->getRoot(),
            $parent,
            $blobFile,
            1,
            $metadata
        );
        $parent->setChild($name, $file);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createTreeReference($name, TreeInterface $parent, ReferenceInterface $reference)
    {
        $file = $this->factory->createTreeReference(
            ltrim(sprintf('%s/%s', $parent->getPath(), $name), '/'),
            $name,
            $parent->getRoot(),
            $parent,
            $reference->getName(),
            $reference->getUser()
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
            } else {
                $this->storeNode($child);
            }
        }

        $this->storeNode($tree);
    }

    /**
     * @param NodeInterface $child
     */
    private function storeNode(NodeInterface $child)
    {
        $this->treeAdapter->storeTree($child);

        $this->cache->save($child->getHash(), $child);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($hash)
    {
        if ($this->cache->contains($hash)) {
            return $this->cache->fetch($hash);
        }

        $data = $this->treeAdapter->fetchTreeData($hash);
        $tree = $this->parseTreeData($hash, $data);
        $this->cache->save($hash, $tree);

        return $tree;
    }

    /**
     * @param array $children
     *
     * @return array
     */
    private function deserializeChildren($children)
    {
        $result = array();
        foreach ($children[TreeInterface::TREE_TYPE] as $name => $childHash) {
            $result[$this->cleanName($name)] = $this->fetchProxy($childHash);
        }
        foreach ($children[TreeInterface::FILE_TYPE] as $name => $childHash) {
            $result[$this->cleanName($name)] = $this->fetchFileProxy($childHash);
        }
        foreach ($children[TreeInterface::REFERENCE_TYPE] as $name => $childHash) {
            $result[$this->cleanName($name)] = $this->fetchReferenceProxy($childHash);
        }

        return $result;
    }

    private function cleanName($name)
    {
        // TODO add ß, ä, ö, perhaps other special chars
        return urldecode(str_replace(array('u%CC%88'), array('ü'), urlencode($name)));
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
        $treeWalker = $this->getTreeWalker($root);
        $children = $this->deserializeChildren($data[TreeInterface::CHILDREN_KEY]);

        return $this->factory->createTree($path, $root, $treeWalker->walk(dirname($path)), $children);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        if ($this->cache->contains($hash)) {
            return $this->cache->fetch($hash);
        }

        $data = $this->treeAdapter->fetchTreeData($hash);

        $path = $data[NodeInterface::PATH_KEY];
        $type = $data[TreeFileInterface::TYPE_KEY];
        if ($type !== NodeInterface::FILE_TYPE) {
            throw new NotAFileException($hash, $path);
        }

        $name = basename($path);
        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);
        $blobFile = $this->blobFileManager->downloadProxy($data[TreeFileInterface::FILE_KEY]);
        $metadata = $data[TreeFileInterface::METADATA_KEY];
        $parent = $this->fetchProxy($data[NodeInterface::PARENT_KEY]);
        $version = $data[TreeFileInterface::VERSION_KEY];

        $treeFile = $this->factory->createTreeFile($path, $name, $root, $parent, $blobFile, $version, $metadata);
        $this->cache->save($hash, $treeFile);

        return $treeFile;
    }

    public function fetchReference($hash)
    {
        if ($this->cache->contains($hash)) {
            return $this->cache->fetch($hash);
        }

        $data = $this->treeAdapter->fetchTreeData($hash);

        $path = $data[NodeInterface::PATH_KEY];
        $type = $data[TreeInterface::TYPE_KEY];
        if ($type !== NodeInterface::REFERENCE_TYPE) {
            throw new NotAReferenceException($path);
        }

        $name = basename($path);
        $root = $this->fetchProxy($data[TreeInterface::ROOT_KEY]);
        $parent = $this->fetchProxy($data[NodeInterface::PARENT_KEY]);
        $referenceName = $data[TreeReferenceInterface::NAME_KEY];
        $username = $data[TreeReferenceInterface::USERNAME_KEY];
        $user = $this->userProvider->loadUserByUsername($username);

        $treeReference = $this->factory->createTreeReference($path, $name, $root, $parent, $referenceName, $user);
        $this->cache->save($hash, $treeReference);

        return $treeReference;
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
            TreeFileInterface::class,
            function () use ($hash) {
                return $this->fetchFile($hash);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function fetchReferenceProxy($hash)
    {
        return $this->factory->createProxy(
            TreeInterface::class,
            function () use ($hash) {
                return $this->fetchReference($hash);
            }
        );
    }
}
