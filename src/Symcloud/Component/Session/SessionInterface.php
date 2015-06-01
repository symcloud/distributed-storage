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

use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Tree\TreeFileInterface;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symcloud\Component\Session\Exception\NotAFileException;

interface SessionInterface
{
    /**
     * @return TreeInterface
     */
    public function init();

    /**
     * @param string $fileName
     * @param $mimeType
     * @param $size
     *
     * @return BlobFileInterface
     */
    public function upload($fileName, $mimeType, $size);

    /**
     * @param string $filePath
     * @param CommitInterface $commit
     *
     * @return TreeFileInterface
     */
    public function download($filePath, CommitInterface $commit = null);

    /**
     * @param CommitInterface $commit
     *
     * @return TreeInterface
     */
    public function getRoot(CommitInterface $commit = null);

    /**
     * @param string $filePath
     * @param BlobFileInterface $blobFile
     *
     * @return TreeFileInterface
     */
    public function createOrUpdateFile($filePath, BlobFileInterface $blobFile);

    /**
     * @param string $filePath
     *
     * @throws NotAFileException
     */
    public function deleteFile($filePath);

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

    /**
     * @param string $hash
     * @param BlobInterface[] $blobs
     * @param string $mimetype
     * @param int $size
     *
     * @return BlobFileInterface
     */
    public function createBlobFile($hash, array $blobs, $mimetype, $size);
}
