<?php

namespace Integration;

use Basho\Riak;
use Basho\Riak\Bucket;
use Basho\Riak\Node\Builder;
use Prophecy\PhpUnit\ProphecyTestCase;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\BlobFileManager;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\FileSplitter;
use Symcloud\Component\FileStorage\FileSplitterInterface;
use Symcloud\Riak\RiakBlobAdapter;
use Symcloud\Riak\RiakBlobFileAdapter;

abstract class BaseIntegrationTest extends ProphecyTestCase
{
    /**
     * @var Riak
     */
    private $riak;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var Bucket
     */
    private $blobBucket;

    /**
     * @var Bucket
     */
    private $fileBucket;

    /**
     * @var BlobAdapterInterface
     */
    private $blobAdapter;

    /**
     * @var BlobManagerInterface
     */
    private $blobManager;

    /**
     * @var BlobFileAdapterInterface
     */
    private $blobFileAdapter;

    /**
     * @var FileSplitterInterface
     */
    private $fileSplitter;

    /**
     * @var int
     */
    private $maxLength = 200;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    /**
     * @var BlobFileManagerInterface
     */
    private $blobFileManager;

    protected function getFileSplitter()
    {
        if (!$this->fileSplitter) {
            $this->fileSplitter = new FileSplitter($this->maxLength);
        }

        return $this->fileSplitter;
    }

    protected function getProxyFactory()
    {
        if (!$this->proxyFactory) {
            $this->proxyFactory = new LazyLoadingValueHolderFactory();
        }

        return $this->proxyFactory;
    }

    protected function getBlobFileAdapter()
    {
        if (!$this->blobFileAdapter) {
            $this->blobFileAdapter = new RiakBlobFileAdapter($this->getRiak(), $this->getFileBucket());
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

    protected function getBlobAdapter()
    {
        if (!$this->blobAdapter) {
            $this->blobAdapter = new RiakBlobAdapter($this->getRiak(), $this->getBlobBucket());
        }

        return $this->blobAdapter;
    }

    protected function getBlobManager()
    {
        if (!$this->blobManager) {
            $this->blobManager = new BlobManager($this->getFactory(), $this->getBlobAdapter());
        }

        return $this->blobManager;
    }

    protected function getFactory()
    {
        if (!$this->factory) {
            $this->factory = new Factory('md5', 'ThisIsMySecretValue', $this->getProxyFactory());
        }

        return $this->factory;
    }

    protected function getRiak()
    {
        if (!$this->riak) {
            $nodes = (new Builder())
                ->buildLocalhost([8098]);

            $this->riak = new Riak($nodes);
        }

        return $this->riak;
    }

    protected function getBlobBucket()
    {
        if (!$this->blobBucket) {
            $this->blobBucket = new Riak\Bucket('test-blobs');
        }

        return $this->blobBucket;
    }

    protected function getFileBucket()
    {
        if (!$this->fileBucket) {
            $this->fileBucket = new Riak\Bucket('test-files');
        }

        return $this->fileBucket;
    }

    protected function clearBucket(Bucket $bucket, Riak $riak)
    {
        $response = $this->fetchBucketKeys($bucket, $riak);

        foreach ($response->getObject()->getData()->keys as $key) {
            $this->deleteObject($key, $bucket, $riak);
        }
    }

    protected function fetchBucketKeys(Bucket $bucket, Riak $riak)
    {
        $fetchObject = (new Riak\Command\Builder\FetchObject($riak))
            ->inBucket($bucket);

        return (new Riak\Command\Bucket\Keys($fetchObject))
            ->execute();
    }

    protected function fetchObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\FetchObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    protected function storeObject($key, $data, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\StoreObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->buildJsonObject($data)
            ->build()
            ->execute();
    }

    protected function deleteObject($key, Bucket $bucket, Riak $riak)
    {
        return (new Riak\Command\Builder\DeleteObject($riak))
            ->atLocation(new Riak\Location($key, $bucket))
            ->build()
            ->execute();
    }

    protected function generateTestFile($length)
    {
        $data = $this->generateString($length);
        $fileName = tempnam('', 'test-file');
        file_put_contents($fileName, $data);

        return array($data, $fileName);
    }

    protected function generateString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randstring;
    }
}
