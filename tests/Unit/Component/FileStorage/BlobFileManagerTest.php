<?php

namespace Unit\Component\FileStorage;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\NoCallsPrediction;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\FileStorage\BlobFileManager;
use Symcloud\Component\FileStorage\FileSplitter;

class BlobFileManagerTest extends ProphecyTestCase
{
    public function testUpload()
    {
        $mimeType = 'application/json';
        $size = 999;

        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $blob1 = new Blob();
        $blob1->setHash('hash1');
        $blob1->setData(substr($data, 0, 100));

        $blob2 = new Blob();
        $blob2->setHash('hash2');
        $blob2->setData(substr($data, 100, 100));

        $file = new BlobFile();
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));
        $file->setMimeType($mimeType);
        $file->setSize($size);

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->upload($blob1->getData())->willReturn($blob1);
        $blobManager->upload($blob2->getData())->willReturn($blob2);
        $blobManager->download()->should(new NoCallsPrediction());
        $blobManager->downloadProxy($blob1->getHash())->willReturn($blob1);
        $blobManager->downloadProxy($blob2->getHash())->willReturn($blob2);

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash($fileName)->willReturn($fileHash);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal()
        );

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
    }

    public function testDownload()
    {
        $mimeType = 'application/json';
        $size = 999;

        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $blob1 = new Blob();
        $blob1->setHash('hash1');
        $blob1->setData(substr($data, 0, 100));

        $blob2 = new Blob();
        $blob2->setHash('hash2');
        $blob2->setData(substr($data, 100, 100));

        $file = new BlobFile();
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));
        $file->setMimeType($mimeType);
        $file->setSize($size);

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->upload()->should(new NoCallsPrediction());
        $blobManager->download()->should(new NoCallsPrediction());
        $blobManager->downloadProxy($blob1->getHash())->willReturn($blob1);
        $blobManager->downloadProxy($blob2->getHash())->willReturn($blob2);

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal()
        );

        $result = $manager->download($fileHash, array($blob1->getHash(), $blob2->getHash()), $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
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
