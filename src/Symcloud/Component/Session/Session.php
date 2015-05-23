<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Session;

use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\Database\Model\Tree\TreeFileInterface;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symcloud\Component\Session\Exception\DirectoryNotExistsException;
use Symcloud\Component\Session\Exception\FileNotExistsException;
use Symcloud\Component\Session\Exception\NotAFileException;
use Symcloud\Component\Session\Exception\NotATreeException;
use Symfony\Component\Security\Core\User\UserInterface;

class Session implements SessionInterface
{
    /**
     * @var BlobFileManagerInterface
     */
    private $blobFileManager;

    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var CommitManagerInterface
     */
    private $commitManager;

    /**
     * @var string
     */
    private $referenceName;

    /**
     * @var ReferenceInterface
     */
    private $reference;

    /**
     * @var CommitInterface
     */
    private $referenceCommit;

    /**
     * @var TreeInterface
     */
    private $root;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * Session constructor.
     *
     * @param BlobFileManagerInterface $blobFileManager
     * @param ReferenceManagerInterface $referenceManager
     * @param TreeManagerInterface $treeManager
     * @param CommitManagerInterface $commitManager
     * @param string $referenceName
     * @param UserInterface $user
     */
    public function __construct(
        BlobFileManagerInterface $blobFileManager,
        ReferenceManagerInterface $referenceManager,
        TreeManagerInterface $treeManager,
        CommitManagerInterface $commitManager,
        $referenceName,
        UserInterface $user
    ) {
        $this->blobFileManager = $blobFileManager;
        $this->referenceManager = $referenceManager;
        $this->treeManager = $treeManager;
        $this->commitManager = $commitManager;
        $this->referenceName = $referenceName;
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $root = $this->treeManager->createRootTree();
        $this->treeManager->store($root);
        $this->referenceCommit = $this->commitManager->commit($root, $this->user, 'init');
        $this->reference = $this->referenceManager->create($this->referenceName, $this->user, $this->referenceCommit);

        return $root;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($fileName, $mimeType, $size)
    {
        $blobFile = $this->blobFileManager->upload($fileName, $mimeType, $size);

        return $blobFile;
    }

    /**
     * {@inheritdoc}
     */
    public function download($filePath, CommitInterface $commit = null)
    {
        $node = $this->getFile($filePath, $commit);

        return $node->getFile();
    }

    /**
     * {@inheritdoc}
     */
    public function downloadByHash($hash)
    {
        return $this->blobFileManager->download($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot(CommitInterface $commit = null)
    {
        if ($commit !== null) {
            return $commit->getTree();
        }

        if (!$this->reference) {
            $this->reference = $this->referenceManager->fetch($this->referenceName);
            $this->referenceCommit = $this->reference->getCommit();
        }

        if (!$this->root) {
            $this->root = $this->referenceCommit->getTree();
        }

        return $this->root;
    }

    private function getTreeWalker($clone = false)
    {
        $tree = $this->getRoot();

        if ($clone) {
            $tree = clone $tree;
            $this->root = $tree;
        }

        $treeWalker = $this->treeManager->getTreeWalker($tree);

        return $treeWalker;
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateFile($filePath, $fileHash)
    {
        $treeWalker = $this->getTreeWalker(true);

        $parentPath = dirname($filePath);
        $fileName = basename($filePath);
        $parentTree = $treeWalker->walk($parentPath);

        if (!$parentTree) {
            $parentTree = $this->createRecursive($parentPath);
        }

        if (!($parentTree instanceof TreeInterface)) {
            throw new NotATreeException($parentPath);
        }

        if (!($child = $parentTree->getChild($fileName))) {
            return $this->treeManager->createTreeFile(
                $fileName,
                $parentTree,
                $this->blobFileManager->downloadProxy($fileHash)
            );
        }

        if (!($child instanceof TreeFileInterface)) {
            throw new NotAFileException($filePath);
        }

        if ($child->getFileHash() === $fileHash) {
            return $child;
        }

        $child->increaseVersion();
        $child->setFile($this->blobFileManager->downloadProxy($fileHash));

        return $child;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($filePath)
    {
        // TODO cleanup
        $treeWalker = $this->getTreeWalker(true);

        $parentPath = dirname($filePath);
        $fileName = basename($filePath);
        if (!($parentTree = $treeWalker->walk($parentPath)) || !($parentTree instanceof TreeInterface)) {
            throw new NotAFileException($filePath);
        }

        $parentTree->removeChild($fileName);
    }

    /**
     * @param string $path
     *
     * @return TreeInterface
     */
    private function createRecursive($path)
    {
        $treeWalker = $this->getTreeWalker();
        $name = basename($path);
        $parentPath = dirname($path);
        if (!($parentTree = $treeWalker->walk($parentPath))) {
            $parentTree = $this->createRecursive($parentPath);
        }

        return $this->treeManager->createTree($name, $parentTree);
    }

    /**
     * {@inheritdoc}
     */
    public function getFile($filePath, CommitInterface $commit = null)
    {
        if (!$commit) {
            $treeWalker = $this->getTreeWalker();
        } else {
            $treeWalker = $this->treeManager->getTreeWalker($commit->getTree());
        }

        $node = $treeWalker->walk($filePath);

        if ($node === null) {
            throw new FileNotExistsException($filePath);
        }

        if (!$node->getIsFile()) {
            throw new NotAFileException($filePath);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function getDirectory($path, CommitInterface $commit = null)
    {
        if (!$commit) {
            $treeWalker = $this->getTreeWalker();
        } else {
            $treeWalker = $this->treeManager->getTreeWalker($commit->getTree());
        }

        $node = $treeWalker->walk($path);

        if ($node === null) {
            throw new DirectoryNotExistsException($path);
        }

        if ($node->getIsFile()) {
            throw new NotATreeException($path);
        }

        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function commit($message = '')
    {
        $this->treeManager->store($this->getRoot());

        $this->referenceCommit = $this->commitManager->commit(
            $this->getRoot(),
            $this->user,
            $message,
            $this->referenceCommit
        );

        if (!$this->reference) {
            $this->reference = $this->referenceManager->fetch($this->referenceName);
        }

        $this->referenceManager->update($this->reference, $this->referenceCommit);

        return $this->referenceCommit;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCommit()
    {
        return $this->referenceCommit;
    }
}
