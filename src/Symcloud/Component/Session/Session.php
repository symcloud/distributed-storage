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

use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeWalkerInterface;
use Symcloud\Component\Session\Exception\FileNotExistsException;
use Symcloud\Component\Session\Exception\NotAFileException;
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
     * @var TreeWalkerInterface
     */
    private $treeWalker;

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
        $this->root = $this->treeManager->createRootTree();
        $this->treeManager->store($this->root);
        $this->referenceCommit = $this->commitManager->commit($this->root, $this->user, 'init');
        $this->reference = $this->referenceManager->create($this->user, $this->referenceCommit, $this->referenceName);

        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($fileName)
    {
        $blobFile = $this->blobFileManager->upload($fileName);

        return $blobFile;
    }

    /**
     * {@inheritdoc}
     */
    public function download($filePath)
    {
        $node = $this->getFile($filePath);

        return $node->getFile();
    }

    /**
     * {@inheritdoc}
     */
    public function getRoot()
    {
        if (!$this->root) {
            $this->reference = $this->referenceManager->getForUser($this->user, $this->referenceName);
            $this->referenceCommit = $this->reference->getCommit();
            $this->root = $this->referenceCommit->getTree();
        }

        return $this->root;
    }

    private function getTreeWalker()
    {
        if (!$this->treeWalker) {
            $this->treeWalker = $this->treeManager->getTreeWalker($this->getRoot());
        }

        return $this->treeWalker;
    }

    /**
     * {@inheritdoc}
     */
    public function createOrUpdateFile($filePath, $fileHash)
    {
        $treeWalker = $this->getTreeWalker();

        $parentPath = dirname($filePath);
        $fileName = basename($filePath);
        if (!($parentTree = $treeWalker->walk($parentPath))) {
            $parentTree = $this->createRecursive($parentPath);
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

        $child->setFile($this->blobFileManager->downloadProxy($fileHash));

        return $child;
    }

    /**
     * @param $path
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
    public function getFile($filePath)
    {
        $treeWalker = $this->getTreeWalker();
        $node = $treeWalker->walk($filePath);

        if ($node === null) {
            throw new FileNotExistsException($filePath);
        }

        if (!$node->isFile()) {
            throw new NotAFileException($filePath);
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
        $this->referenceManager->update($this->reference, $this->referenceCommit);
    }
}
