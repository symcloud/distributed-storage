<?php

namespace Symcloud\Component\FileStorage;

use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\FactoryInterface;

class FileManager
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
     * @var FileAdapterInterface
     */
    private $adapter;

    /**
     * FileManager constructor.
     * @param FileSplitterInterface $fileSplitter
     * @param BlobManagerInterface $blobManager
     * @param FactoryInterface $factory
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

    public function upload($filePath)
    {
        $fileHash = $this->factory->createFileHash($filePath);
        $blobs = array();

        $this->fileSplitter->split(
            $filePath,
            function ($index, $data) use (&$blobs) {
                $blobs[] = $this->uploadChunk($index, $data);
            }
        );

        $file = $this->factory->createFile($fileHash, $blobs);
        $this->adapter->storeFile($file->getHash(), $file->getBlobs());

        return $file;
    }

    private function uploadChunk($index, $data)
    {
        return $this->blobManager->uploadBlob($data);
    }
}
