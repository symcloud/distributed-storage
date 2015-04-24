<?php

namespace Integration\Parts;

use Basho\Riak\Bucket;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\BlobFileManager;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\FileSplitter;
use Symcloud\Component\FileStorage\FileSplitterInterface;
use Symcloud\Riak\RiakBlobFileAdapter;

trait BlobFileManagerTrait
{
    use BlobManagerTrait;

    /**
     * @var BlobFileManagerInterface
     */
    private $blobFileManager;

    /**
     * @var FileSplitterInterface
     */
    private $fileSplitter;

    /**
     * @var BlobFileAdapterInterface
     */
    private $blobFileAdapter;

    /**
     * @var Bucket
     */
    private $blobFileBucket;

    protected function getBlobMaxLength()
    {
        return 100;
    }

    protected function getBlobFileBucket()
    {
        if (!$this->blobFileBucket) {
            $this->blobFileBucket = new Bucket('test-files');
        }

        return $this->blobFileBucket;
    }

    protected function getFileSplitter()
    {
        if (!$this->fileSplitter) {
            $this->fileSplitter = new FileSplitter($this->getBlobMaxLength());
        }

        return $this->fileSplitter;
    }

    protected function getBlobFileAdapter()
    {
        if (!$this->blobFileAdapter) {
            $this->blobFileAdapter = new RiakBlobFileAdapter($this->getRiak(), $this->getBlobFileBucket());
        }

        return $this->blobFileAdapter;
    }

    protected function getBlobFileManager()
    {
        if (!$this->blobFileManager) {
            $this->blobFileManager = new BlobFileManager(
                $this->getFileSplitter(),
                $this->getBlobManager(),
                $this->getFactory(),
                $this->getBlobFileAdapter(),
                $this->getProxyFactory()
            );
        }

        return $this->blobFileManager;
    }
}
