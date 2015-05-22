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
