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

use Symcloud\Component\Database\Model\ChunkFileInterface;
use Symcloud\Component\Database\Model\ChunkInterface;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
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
     * @return bool
     */
    public function isInit();

    /**
     * @param string $fileName
     * @param $mimeType
     * @param $size
     *
     * @return ChunkFileInterface
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
     * @param ChunkFileInterface $chunkFile
     *
     * @return TreeFileInterface
     */
    public function createOrUpdateFile($filePath, ChunkFileInterface $chunkFile);

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
     * @return ReferenceInterface
     */
    public function getReference();

    /**
     * @return ReferenceInterface[]
     */
    public function getReferences();

    /**
     * @param string $hash
     * @param ChunkInterface[] $chunks
     * @param string $mimetype
     * @param int $size
     *
     * @return ChunkFileInterface
     */
    public function createChunkFile($hash, array $chunks, $mimetype, $size);
}
