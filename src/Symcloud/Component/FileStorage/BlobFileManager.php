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

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;

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
     * @var BlobFileAdapterInterface
     */
    private $adapter;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    /**
     * BlobFileManager constructor.
     *
     * @param FileSplitterInterface         $fileSplitter
     * @param BlobManagerInterface          $blobManager
     * @param FactoryInterface              $factory
     * @param BlobFileAdapterInterface      $adapter
     * @param LazyLoadingValueHolderFactory $proxyFactory
     */
    public function __construct(
        FileSplitterInterface $fileSplitter,
        BlobManagerInterface $blobManager,
        FactoryInterface $factory,
        BlobFileAdapterInterface $adapter,
        LazyLoadingValueHolderFactory $proxyFactory
    ) {
        $this->fileSplitter = $fileSplitter;
        $this->blobManager = $blobManager;
        $this->factory = $factory;
        $this->adapter = $adapter;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upload($filePath)
    {
        $fileHash = $this->factory->createFileHash($filePath);

        if ($this->adapter->fileExists($fileHash)) {
            return $this->download($fileHash);
        }

        $blobs = array();
        $blobKeys = array();

        $this->fileSplitter->split(
            $filePath,
            function ($index, $data) use (&$blobs, &$blobKeys) {
                $blob = $this->uploadChunk($data);
                $blobs[$index] = $this->getBlobProxy($blob->getHash());
                $blobKeys[$index] = $blob->getHash();

                // unset blob to save memory
                unset($blob);
            }
        );

        $file = $this->factory->createBlobFile($fileHash, $blobs);
        $this->adapter->storeFile($file->getHash(), $blobKeys);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function download($fileHash)
    {
        $blobKeys = $this->adapter->fetchFile($fileHash);
        $blobs = array();

        foreach ($blobKeys as $key) {
            $blobs[] = $this->getBlobProxy($key);
        }

        return $this->factory->createBlobFile($fileHash, $blobs);
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

    private function getBlobProxy($hash)
    {
        return $this->factory->createProxy(
            BlobInterface::class,
            function () use ($hash) {
                return $this->blobManager->downloadBlob($hash);
            }
        );
    }

    private function uploadChunk($data)
    {
        return $this->blobManager->uploadBlob($data);
    }
}
