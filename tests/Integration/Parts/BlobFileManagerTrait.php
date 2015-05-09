<?php

namespace Integration\Parts;

use Riak\Client\Core\Query\RiakNamespace;
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
     * @var RiakNamespace
     */
    private $blobFileNamespace;

    protected function getBlobMaxLength()
    {
        return 100;
    }

    protected function getBlobFileNamespace()
    {
        if (!$this->blobFileNamespace) {
            $this->blobFileNamespace = new RiakNamespace(RiakNamespace::DEFAULT_TYPE, 'test-files');
        }

        return $this->blobFileNamespace;
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
            $this->blobFileAdapter = new RiakBlobFileAdapter($this->getRiak(), $this->getBlobFileNamespace());
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
