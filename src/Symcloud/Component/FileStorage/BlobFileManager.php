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

use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\BlobFileInterface;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;

class BlobFileManager implements BlobFileManagerInterface
{
    /**
     * @var FileSplitterInterface
     */
    private $fileSplitter;

    /**
     * @var BlobManagerInterface
     */
    private $blobManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * BlobFileManager constructor.
     *
     * @param FileSplitterInterface         $fileSplitter
     * @param BlobManagerInterface          $blobManager
     * @param FactoryInterface              $factory
     * @param DatabaseInterface $database
     */
    public function __construct(
        FileSplitterInterface $fileSplitter,
        BlobManagerInterface $blobManager,
        FactoryInterface $factory,
        DatabaseInterface $database
    ) {
        $this->fileSplitter = $fileSplitter;
        $this->blobManager = $blobManager;
        $this->factory = $factory;
        $this->database = $database;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($filePath, $mimeType, $size)
    {
        $fileHash = $this->factory->createFileHash($filePath);
        $hits = $this->database->search('fileHash:' . $fileHash, array('file'));

        if (sizeof($hits) === 1) {
            return $this->doDownload($hits[0]->getHash());
        }

        $blobs = array();
        $this->fileSplitter->split(
            $filePath,
            function ($index, $data) use (&$blobs) {
                $blob = $this->uploadChunk($data);
                $blobs[$index] = $this->blobManager->downloadProxy($blob->getHash());

                // unset blob to save memory
                unset($blob);
            }
        );

        $file = new BlobFile();
        $file->setPolicyCollection(new PolicyCollection());
        $file->setSize($size);
        $file->setMimetype($mimeType);
        $file->setBlobs($blobs);
        $file->setFileHash($fileHash);

        return $this->database->store($file);
    }

    /**
     * {@inheritdoc}
     */
    public function download($fileHash)
    {
        $hits = $this->database->search('fileHash:' . $fileHash, array('file'));

        if (sizeof($hits) < 1) {
            throw new FileNotFoundException($fileHash);
        }

        return $this->doDownload($hits[0]->getHash());
    }

    private function doDownload($hash)
    {
        try {
            return $this->database->fetch($hash, BlobFile::class);
        } catch (\Exception $ex) {
            throw new FileNotFoundException($hash);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function downloadProxy($hash)
    {
        return $this->factory->createProxy(
            BlobFileInterface::class,
            function () use ($hash) {
                return $this->download($hash);
            }
        );
    }

    /**
     * @param $data
     *
     * @return BlobInterface
     */
    private function uploadChunk($data)
    {
        return $this->blobManager->upload($data);
    }
}
