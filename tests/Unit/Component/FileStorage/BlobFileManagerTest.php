<?php

namespace Unit\Component\FileStorage;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\NoCallsPrediction;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\BlobFileManager;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;
use Symcloud\Component\FileStorage\FileSplitter;
use Symcloud\Component\FileStorage\Model\BlobFileModel;

class BlobFileManagerTest extends ProphecyTestCase
{
    public function testUpload()
    {
        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $blob1 = new BlobModel();
        $blob1->setHash('hash1');
        $blob1->setData(substr($data, 0, 100));

        $blob2 = new BlobModel();
        $blob2->setHash('hash2');
        $blob2->setData(substr($data, 100, 100));

        $file = new BlobFileModel();
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobFileAdapterInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->uploadBlob($blob1->getData())->willReturn($blob1);
        $blobManager->uploadBlob($blob2->getData())->willReturn($blob2);
        $blobManager->downloadBlob()->should(new NoCallsPrediction());

        $factory->createBlob()->should(new NoCallsPrediction());
        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash($fileName)->willReturn($fileHash);
        $factory->createBlobFile($fileHash, Argument::size(2))->willReturn($file);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $adapter->storeFile($fileHash, Argument::size(2))->willReturn(true);
        $adapter->fileExists($fileHash)->willReturn(false);
        $adapter->fetchFile()->should(new NoCallsPrediction());

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $adapter->reveal(),
            $proxyFactory
        );

        $result = $manager->upload($fileName);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
    }

    public function testUploadExisting()
    {
        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $blob1 = new BlobModel();
        $blob1->setHash('hash1');
        $blob1->setData(substr($data, 0, 100));

        $blob2 = new BlobModel();
        $blob2->setHash('hash2');
        $blob2->setData(substr($data, 100, 100));

        $file = new BlobFileModel();
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobFileAdapterInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->uploadBlob()->should(new NoCallsPrediction());
        $blobManager->downloadBlob()->should(new NoCallsPrediction());

        $factory->createBlob()->should(new NoCallsPrediction());
        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash($fileName)->willReturn($fileHash);
        $factory->createBlobFile($fileHash, Argument::size(2))->willReturn($file);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $adapter->storeFile()->should(new NoCallsPrediction());
        $adapter->fileExists($fileHash)->willReturn(true);
        $adapter->fetchFile($fileHash)->willReturn(array($blob1->getHash(), $blob2->getHash()));

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $adapter->reveal(),
            $proxyFactory
        );

        $result = $manager->upload($fileName);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
    }

    public function testDownload()
    {
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $blob1 = new BlobModel();
        $blob1->setHash('hash1');
        $blob1->setData(substr($data, 0, 100));

        $blob2 = new BlobModel();
        $blob2->setHash('hash2');
        $blob2->setData(substr($data, 100, 100));

        $file = new BlobFileModel();
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobFileAdapterInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->uploadBlob()->should(new NoCallsPrediction());
        $blobManager->downloadBlob()->should(new NoCallsPrediction());

        $factory->createBlob()->should(new NoCallsPrediction());
        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());
        $factory->createBlobFile($fileHash, Argument::size(2))->willReturn($file);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $adapter->storeFile()->should(new NoCallsPrediction());
        $adapter->fileExists()->should(new NoCallsPrediction());
        $adapter->fetchFile($fileHash)->willReturn(array($blob1->getHash(), $blob2->getHash()));

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $adapter->reveal(),
            $proxyFactory
        );

        $result = $manager->download($fileHash);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
    }

    /**
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     */
    public function testDownloadNotExists()
    {
        $fileHash = 'my-hash';

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobFileAdapterInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->uploadBlob()->should(new NoCallsPrediction());
        $blobManager->downloadBlob()->should(new NoCallsPrediction());

        $factory->createBlob()->should(new NoCallsPrediction());
        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());
        $factory->createBlobFile()->should(new NoCallsPrediction());

        $adapter->storeFile()->should(new NoCallsPrediction());
        $adapter->fileExists()->should(new NoCallsPrediction());
        $adapter->fetchFile($fileHash)->willThrow(new FileNotFoundException($fileHash));

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $adapter->reveal(),
            $proxyFactory
        );

        $manager->download($fileHash);
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
