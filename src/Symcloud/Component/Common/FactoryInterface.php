<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Common;

use Symcloud\Component\Access\Model\FileInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface FactoryInterface
{
    /**
     * @param string      $data
     * @param string|null $hash
     *
     * @return BlobInterface
     */
    public function createBlob($data, $hash = null);

    /**
     * @param $data
     *
     * @return string
     */
    public function createHash($data);

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function createFileHash($filePath);

    /**
     * @param string          $hash
     * @param BlobInterface[] $blobs
     *
     * @return BlobFileInterface
     */
    public function createBlobFile($hash, $blobs = array());

    /**
     * @param MetadataInterface   $metadata
     * @param FileObjectInterface $object
     * @param BlobFileInterface   $blobFile
     *
     * @return FileInterface
     */
    public function createFile(MetadataInterface $metadata, FileObjectInterface $object, BlobFileInterface $blobFile);

    /**
     * @param string $path
     * @param TreeInterface $root
     * @param array $children
     * @param string|null $hash
     *
     * @return TreeInterface
     */
    public function createTree($path, TreeInterface $root, $children = array(), $hash = null);

    /**
     * @return TreeInterface
     */
    public function createRootTree();

    /**
     * @param string $path
     * @param string $name
     * @param TreeInterface $root
     * @param BlobFileInterface $blobFile
     * @param array $metadata
     * @param string|null $hash
     *
     * @return TreeFileInterface
     */
    public function createTreeFile(
        $path,
        $name,
        TreeInterface $root,
        BlobFileInterface $blobFile,
        $metadata = array(),
        $hash = null
    );

    /**
     * @param TreeInterface   $tree
     * @param UserInterface   $user
     * @param \DateTime       $createdAt
     * @param string          $message
     * @param CommitInterface $parentCommit
     *
     * @return CommitInterface
     */
    public function createCommit(
        TreeInterface $tree,
        UserInterface $user,
        \DateTime $createdAt,
        $message = '',
        CommitInterface $parentCommit = null
    );

    /**
     * @param CommitInterface $commit
     * @param UserInterface   $user
     * @param string          $name
     *
     * @return ReferenceInterface
     */
    public function createReference(CommitInterface $commit, UserInterface $user, $name);

    /**
     * @param string   $className
     * @param callable $initializerCallback
     *
     * @return mixed
     */
    public function createProxy($className, callable $initializerCallback);
}
