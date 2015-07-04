<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage;

use Symcloud\Component\ChunkStorage\ChunkManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\Model\ChunkFile;
use Symcloud\Component\Database\Model\ChunkInterface;

class ChunkFileManager implements ChunkFileManagerInterface
{
    /**
     * @var FileSplitterInterface
     */
    private $fileSplitter;

    /**
     * @var ChunkManagerInterface
     */
    private $chunkManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * ChunkFileManager constructor.
     *
     * @param FileSplitterInterface         $fileSplitter
     * @param ChunkManagerInterface          $chunkManager
     * @param FactoryInterface              $factory
     */
    public function __construct(
        FileSplitterInterface $fileSplitter,
        ChunkManagerInterface $chunkManager,
        FactoryInterface $factory
    ) {
        $this->fileSplitter = $fileSplitter;
        $this->chunkManager = $chunkManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($filePath, $mimeType, $size)
    {
        $fileHash = $this->factory->createFileHash($filePath);

        $chunks = array();
        $this->fileSplitter->split(
            $filePath,
            function ($index, $data) use (&$chunks) {
                $chunk = $this->uploadChunk($data);
                $chunks[$index] = $this->chunkManager->downloadProxy($chunk->getHash());

                // unset chunk to save memory
                unset($chunk);
            }
        );

        $file = new ChunkFile();
        $file->setHash($fileHash);
        $file->setSize($size);
        $file->setMimetype($mimeType);
        $file->setChunks($chunks);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function download($hash, array $chunks, $mimetype, $size)
    {
        $file = new ChunkFile();
        $file->setHash($hash);
        $file->setMimetype($mimetype);
        $file->setSize($size);

        $result = array();
        foreach ($chunks as $chunkHash) {
            $result[] = $this->chunkManager->downloadProxy($chunkHash);
        }
        $file->setChunks($result);

        return $file;
    }

    /**
     * @param $data
     *
     * @return ChunkInterface
     */
    private function uploadChunk($data)
    {
        return $this->chunkManager->upload($data);
    }
}
