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

        list($data, $fileName) = $this->generateTestFile(200);
        $blob1 = $factory->createBlob(substr($data, 0, 100));
        $blob2 = $factory->createBlob(substr($data, 100, 100));
        $fileHash = $factory->createFileHash($fileName);

        $manager = new FileManager($fileSplitter, $blobManager, $factory, $fileAdapter, $proxyFactory);

        $result = $manager->upload($fileName);

        $this->assertEquals($factory->createHash($data), $result->getHash());
        $this->assertEquals($blob1->getHash(), $result->getBlobs()[0]->getHash());
        $this->assertEquals($blob1->getData(), $result->getBlobs()[0]->getData());
        $this->assertEquals($blob2->getHash(), $result->getBlobs()[1]->getHash());
        $this->assertEquals($blob2->getData(), $result->getBlobs()[1]->getData());
        $this->assertEquals($data, $result->getContent());

        $fileKeys = $this->fetchBucketKeys($fileBucket, $riak)->getObject()->getData()->keys;
        $blobKeys = $this->fetchBucketKeys($blobBucket, $riak)->getObject()->getData()->keys;

        $this->assertContains($result->getBlobs()[0]->getHash(), $blobKeys);
        $this->assertContains($result->getBlobs()[1]->getHash(), $blobKeys);
        $this->assertContains($result->getHash(), $fileKeys);

        $this->assertEquals($blob1->getData(), $this->fetchObject($blob1->getHash(), $blobBucket, $riak)->getObject()->getData());
        $this->assertEquals($blob2->getData(), $this->fetchObject($blob2->getHash(), $blobBucket, $riak)->getObject()->getData());

        $this->assertEquals(array($blob1->getHash(), $blob2->getHash()), $this->fetchObject($fileHash, $fileBucket, $riak)->getObject()->getData());
    }

    private function generateTestFile($length)
    {
        $data = $this->generateString($length);
        $fileName = tempnam('', 'test-file');
        file_put_contents($fileName, $data);

        return array($data, $fileName);
    }

    private function generateString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randstring;
    }
}
