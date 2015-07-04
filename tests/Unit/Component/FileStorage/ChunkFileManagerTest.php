<?php

namespace Unit\Component\FileStorage;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\NoCallsPrediction;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\ChunkStorage\ChunkManagerInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkFile;
use Symcloud\Component\FileStorage\ChunkFileManager;
use Symcloud\Component\FileStorage\FileSplitter;

class ChunkFileManagerTest extends ProphecyTestCase
{
    public function testUpload()
    {
        $mimeType = 'application/json';
        $size = 999;

        $data = $this->generateString(200);
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $fileHash = 'my-hash';

        $chunk1 = new Chunk();
        $chunk1->setHash('hash1');
        $chunk1->setData(substr($data, 0, 100));

        $chunk2 = new Chunk();
        $chunk2->setHash('hash2');
        $chunk2->setData(substr($data, 100, 100));

        $file = new ChunkFile();
        $file->setHash($fileHash);
        $file->setChunks(array($chunk1, $chunk2));
        $file->setMimeType($mimeType);
        $file->setSize($size);

        $fileSplitter = new FileSplitter(100);
        $chunkManager = $this->prophesize(ChunkManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $chunkManager->upload($chunk1->getData())->willReturn($chunk1);
        $chunkManager->upload($chunk2->getData())->willReturn($chunk2);
        $chunkManager->download()->should(new NoCallsPrediction());
        $chunkManager->downloadProxy($chunk1->getHash())->willReturn($chunk1);
        $chunkManager->downloadProxy($chunk2->getHash())->willReturn($chunk2);

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash($fileName)->willReturn($fileHash);
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $manager = new ChunkFileManager(
            $fileSplitter,
            $chunkManager->reveal(),
            $factory->reveal()
        );

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getChunks(), $result->getChunks());
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

        $chunk1 = new Chunk();
        $chunk1->setHash('hash1');
        $chunk1->setData(substr($data, 0, 100));

        $chunk2 = new Chunk();
        $chunk2->setHash('hash2');
        $chunk2->setData(substr($data, 100, 100));

        $file = new ChunkFile();
        $file->setHash($fileHash);
        $file->setChunks(array($chunk1, $chunk2));
        $file->setMimeType($mimeType);
        $file->setSize($size);

        $fileSplitter = new FileSplitter(100);
        $chunkManager = $this->prophesize(ChunkManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $proxyFactory = new LazyLoadingValueHolderFactory();

        $chunkManager->upload()->should(new NoCallsPrediction());
        $chunkManager->download()->should(new NoCallsPrediction());
        $chunkManager->downloadProxy($chunk1->getHash())->willReturn($chunk1);
        $chunkManager->downloadProxy($chunk2->getHash())->willReturn($chunk2);

        $factory->createHash()->should(new NoCallsPrediction());
        $factory->createFileHash()->should(new NoCallsPrediction());
        $factory->createProxy(Argument::type('string'), Argument::type('callable'))->will(
            function ($args) use ($proxyFactory) {
                return $proxyFactory->createProxy($args[0], $args[1]);
            }
        );

        $manager = new ChunkFileManager(
            $fileSplitter,
            $chunkManager->reveal(),
            $factory->reveal()
        );

        $result = $manager->download($fileHash, array($chunk1->getHash(), $chunk2->getHash()), $mimeType, $size);

        $this->assertEquals($file->getHash(), $result->getHash());
        $this->assertEquals($file->getChunks(), $result->getChunks());
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
