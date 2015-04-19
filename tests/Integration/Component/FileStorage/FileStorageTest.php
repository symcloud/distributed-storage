<?php

namespace Integration\Component\FileStorage;

use Basho\Riak\Node\Builder;
use Integration\BaseIntegrationTest;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\FileStorage\FileManager;
use Symcloud\Component\FileStorage\FileSplitter;
use Symcloud\Riak\RiakBlobAdapter;
use Symcloud\Riak\RiakFileAdapter;

class FileStorageTest extends BaseIntegrationTest
{
    public function testUpload()
    {
        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();
        $fileBucket = $this->getFileBucket();

        $this->clearBucket($blobBucket, $riak);
        $this->clearBucket($fileBucket, $riak);

        $factory = $this->getFactory();
        $blobAdapter = new RiakBlobAdapter($riak, $blobBucket);

        $fileSplitter = new FileSplitter(100);
        $blobManager = new BlobManager($factory, $blobAdapter);

        $fileAdapter = new RiakFileAdapter($riak, $fileBucket);

        $proxyFactory = new LazyLoadingValueHolderFactory();

        $manager = new FileManager($fileSplitter, $blobManager, $factory, $fileAdapter, $proxyFactory);
    }
}
