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

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeReferenceInterface;
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
     * @param string $hash
     * @param BlobInterface[] $blobs
     * @param string $mimeType
     * @param int $size
     *
     * @return BlobFileInterface
     */
    public function createBlobFile($hash, $blobs = array(), $mimeType, $size);

    /**
     * @param string $path
     * @param array $children
     *
     * @return TreeInterface
     */
    public function createTree($path, $children = array());

    /**
     * @return TreeInterface
     */
    public function createRootTree();

    /**
     * @param string $path
     * @param string $name
     * @param BlobFileInterface $blobFile
     * @param int $version
     * @param array $metadata
     *
     * @return TreeFileInterface
     */
    public function createTreeFile(
        $path,
        $name,
        BlobFileInterface $blobFile,
        $version,
        $metadata = array()
    );

    /**
     * @param string $path
     * @param string $name
     * @param string $referenceName
     * @param UserInterface $user
     *
     * @return TreeReferenceInterface
     */
    public function createTreeReference(
        $path,
        $name,
        $referenceName,
        UserInterface $user
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
