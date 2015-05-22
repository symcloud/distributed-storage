<?php

namespace Unit\Component\FileStorage;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\NoCallsPrediction;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobFile;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\FileStorage\BlobFileManager;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;
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
        $file->setPolicy(new Policy());
        $file->setHash($fileHash);
        $file->setBlobs(array($blob1, $blob2));
        $file->setMimeType($mimeType);
        $file->setSize($size);

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $database = $this->prophesize(DatabaseInterface::class);
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

        $database->store($file)->willReturn($file);
        $database->contains($fileHash)->willReturn(false);
        $database->fetch()->should(new NoCallsPrediction());

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $database->reveal()
        );

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
        $this->assertEquals('application/json', $result->getMimeType());
        $this->assertEquals(999, $result->getSize());
    }

    public function testUploadExisting()
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
        $file->setSize($size);
        $file->setMimeType($mimeType);

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $database = $this->prophesize(DatabaseInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->upload()->should(new NoCallsPrediction());
        $blobManager->download()->should(new NoCallsPrediction());

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash($fileName)->willReturn($fileHash);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $database->store()->should(new NoCallsPrediction());
        $database->contains($fileHash)->willReturn(true);
        $database->fetch($fileHash, BlobFile::class)->willReturn($file);

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $database->reveal()
        );

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getBlobs(), $result->getBlobs());
        $this->assertEquals($file->getMimeType(), $result->getMimeType());
        $this->assertEquals($file->getSize(), $result->getSize());
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
        $file->setSize($size);
        $file->setMimeType($mimeType);

        $fileSplitter = new FileSplitter(100);
        $blobManager = $this->prophesize(BlobManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $database = $this->prophesize(DatabaseInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $blobManager->upload()->should(new NoCallsPrediction());
        $blobManager->download()->should(new NoCallsPrediction());

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $database->store()->should(new NoCallsPrediction());
        $database->contains($fileHash)->willReturn(true);
        $database->fetch($fileHash, BlobFile::class)->willReturn($file);

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $database->reveal()
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
        $database = $this->prophesize(DatabaseInterface::class);

        $blobManager->upload()->should(new NoCallsPrediction());
        $blobManager->download()->should(new NoCallsPrediction());

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());

        $database->store()->should(new NoCallsPrediction());
        $database->fetch($fileHash, BlobFile::class)->willThrow(new FileNotFoundException($fileHash));

        $manager = new BlobFileManager(
            $fileSplitter,
            $blobManager->reveal(),
            $factory->reveal(),
            $database->reveal()
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
