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
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\BlobInterface;

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
     * BlobFileManager constructor.
     *
     * @param FileSplitterInterface         $fileSplitter
     * @param BlobManagerInterface          $blobManager
     * @param FactoryInterface              $factory
     */
    public function __construct(
        FileSplitterInterface $fileSplitter,
        BlobManagerInterface $blobManager,
        FactoryInterface $factory
    ) {
        $this->fileSplitter = $fileSplitter;
        $this->blobManager = $blobManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($filePath, $mimeType, $size)
    {
        $fileHash = $this->factory->createFileHash($filePath);

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
        $file->setHash($fileHash);
        $file->setSize($size);
        $file->setMimetype($mimeType);
        $file->setBlobs($blobs);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function download($hash, array $blobs, $mimetype, $size)
    {
        $file = new BlobFile();
        $file->setHash($hash);
        $file->setMimetype($mimetype);
        $file->setSize($size);

        $result = array();
        foreach ($blobs as $blobHash) {
            $result[] = $this->blobManager->downloadProxy($blobHash);
        }
        $file->setBlobs($result);

        return $file;
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
