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

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeReferenceInterface;
use Symcloud\Component\Session\Exception\NotAFileException;
use Symfony\Component\Security\Core\User\UserInterface;

interface SessionInterface
{
    /**
     * @return TreeInterface
     */
    public function init();

    /**
     * @param string $fileName
     *
     * @return BlobFileInterface
     */
    public function upload($fileName, $mimeType, $size);

    /**
     * @param string $filePath
     * @param CommitInterface $commit
     *
     * @return BlobFileInterface
     */
    public function download($filePath, CommitInterface $commit = null);

    /**
     * @param string $hash
     *
     * @return BlobFileInterface
     */
    public function downloadByHash($hash);

    /**
     * @param CommitInterface $commit
     *
     * @return TreeInterface
     */
    public function getRoot(CommitInterface $commit = null);

    /**
     * @param string $filePath
     * @param string $fileHash
     *
     * @return TreeFileInterface
     */
    public function createOrUpdateFile($filePath, $fileHash);

    /**
     * @param string $filePath
     *
     * @throws NotAFileException
     */
    public function deleteFile($filePath);

    /**
     * @param string $path
     * @param UserInterface $user
     * @param string $referenceName
     *
     * @return TreeReferenceInterface
     */
    public function mount($path, UserInterface $user, $referenceName);

    /**
     * @param string $path
     * @param string $referenceName
     *
     * @return ReferenceInterface
     */
    public function split($path, $referenceName);

    /**
     * @param string $filePath
     * @param CommitInterface $commit
     *
     * @return TreeFileInterface
     */
    public function getFile($filePath, CommitInterface $commit = null);

    /**
     * @param string $path
     * @param CommitInterface $commit
     *
     * @return TreeInterface
     */
    public function getDirectory($path, CommitInterface $commit = null);

    /**
     * @param string $message
     *
     * @return CommitInterface
     */
    public function commit($message = '');

    /**
     * @return CommitInterface
     */
    public function getCurrentCommit();
}
