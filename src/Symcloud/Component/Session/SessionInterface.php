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
use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;

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
    public function upload($fileName);

    /**
     * @param $filePath
     * @param CommitInterface $commit
     *
     * @return BlobFileInterface
     */
    public function download($filePath, CommitInterface $commit = null);

    /**
     * @return TreeInterface
     */
    public function getRoot();

    /**
     * @param string $filePath
     * @param string $fileHash
     *
     * @return TreeFileInterface
     */
    public function createOrUpdateFile($filePath, $fileHash);

    /**
     * @param string $filePath
     * @param CommitInterface $commit
     *
     * @return TreeFileInterface
     */
    public function getFile($filePath, CommitInterface $commit = null);

    /**
     * @param string $message
     *
     * @return CommitInterface
     */
    public function commit($message = '');
}
