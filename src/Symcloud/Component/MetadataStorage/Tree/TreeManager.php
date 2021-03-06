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
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\ChunkFileInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\Database\Model\Tree\Tree;
use Symcloud\Component\Database\Model\Tree\TreeFile;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symcloud\Component\Database\Model\Tree\TreeNodeInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TreeManager implements TreeManagerInterface
{
    /**
     * @var DatabaseInterface
     */
    private $database;

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
     * @param DatabaseInterface $database
     * @param UserProviderInterface $userProvider
     * @param FactoryInterface $factory
     */
    public function __construct(
        DatabaseInterface $database,
        UserProviderInterface $userProvider,
        FactoryInterface $factory
    ) {
        $this->database = $database;
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
        $tree = new Tree();
        $tree->setPolicyCollection(new PolicyCollection());
        $tree->setName('');
        $tree->setPath('/');

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createTree($name, TreeInterface $parent)
    {
        $tree = new Tree();
        $tree->setPolicyCollection(new PolicyCollection());
        $tree->setName($name);
        $tree->setPath(sprintf('%s/%s', $parent->getPath(), $name));

        $parent->setChild($name, $tree);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createTreeFile($name, TreeInterface $parent, ChunkFileInterface $chunkFile, $metadata = array())
    {
        $treeFile = new TreeFile();
        $treeFile->setPolicyCollection(new PolicyCollection());
        $treeFile->setName($name);
        $treeFile->setPath('/' . ltrim(sprintf('%s/%s', $parent->getPath(), $name), '/'));
        $treeFile->setFile($chunkFile);
        $treeFile->setVersion(1);
        if ($metadata !== null) {
            $treeFile->setMetadata($metadata);
        }

        $parent->setChild($name, $treeFile);

        return $treeFile;
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
     * @param TreeNodeInterface $child
     */
    private function storeNode(TreeNodeInterface $child)
    {
        $this->database->store($child);

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

        $tree = $this->database->fetch($hash, Tree::class);
        $this->cache->save($hash, $tree);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        if ($this->cache->contains($hash)) {
            return $this->cache->fetch($hash);
        }

        $treeFile = $this->database->fetch($hash, TreeFile::class);
        $this->cache->save($hash, $treeFile);

        return $treeFile;
    }
}
