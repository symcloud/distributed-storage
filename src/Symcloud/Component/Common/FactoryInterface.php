<?php

namespace Symcloud\Component\Common;

use Symcloud\Component\Access\Model\FileInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface FactoryInterface
{
    /**
     * @param string $data
     * @param string|null $hash
     * @return BlobInterface
     */
    public function createBlob($data, $hash = null);

    /**
     * @param $data
     * @return string
     */
    public function createHash($data);

    /**
     * @param string $filePath
     * @return string
     */
    public function createFileHash($filePath);

    /**
     * @param string $hash
     * @param BlobInterface[] $blobs
     * @return BlobFileInterface
     */
    public function createBlobFile($hash, $blobs = array());

    /**
     * @param MetadataInterface $metadata
     * @param FileObjectInterface $object
     * @param BlobFileInterface $blobFile
     * @return FileInterface
     */
    public function createFile(MetadataInterface $metadata, FileObjectInterface $object, BlobFileInterface $blobFile);

    /**
     * @param TreeInterface $tree
     * @param UserInterface $user
     * @param string $message
     * @param CommitInterface $parentCommit
     * @return CommitInterface
     */
    public function createCommit(TreeInterface $tree, UserInterface $user, $message = '', CommitInterface $parentCommit = null);

    /**
     * @param string $className
     * @param callable $initializerCallback
     * @return mixed
     */
    public function createProxy($className, callable $initializerCallback);
}
