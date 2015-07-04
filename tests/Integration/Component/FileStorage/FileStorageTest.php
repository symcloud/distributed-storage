<?php

namespace Integration\Component\FileStorage;

use Integration\Parts\ChunkFileManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkInterface;
use Symcloud\Component\Database\Search\SearchAdapterInterface;

class FileStorageTest extends ProphecyTestCase
{
    use TestFileTrait, ChunkFileManagerTrait;

    private $searchAdapterMock;

    public function storageProvider()
    {
        $factory = $this->getFactory();

        $size = 200;
        $mimeType = 'application/json';
        list($data, $fileName) = $this->generateTestFile($size);
        $chunk1 = new Chunk();
        $chunk1->setData(substr($data, 0, 100));
        $chunk1->setHash($factory->createHash($chunk1->getData()));
        $chunk1->setLength(strlen($chunk1->getData()));

        $chunk2 = new Chunk();
        $chunk2->setData(substr($data, 100, 100));
        $chunk2->setHash($factory->createHash($chunk2->getData()));
        $chunk2->setLength(strlen($chunk2->getData()));

        $chunks = array($chunk1, $chunk2);
        $fileHash = $factory->createFileHash($fileName);

        return array(
            array(
                $fileName,
                $data,
                $fileHash,
                $chunks,
                $mimeType,
                $size
            )
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param string $fileName
     * @param string $data
     * @param string $fileHash
     * @param ChunkInterface[] $chunks
     * @param $mimeType
     * @param $size
     */
    public function testUpload(
        $fileName,
        $data,
        $fileHash,
        $chunks,
        $mimeType,
        $size
    ) {
        $manager = $this->getChunkFileManager();
        $database = $this->getDatabase();
        $factory = $this->getFactory();

        $result = $manager->upload($fileName, $mimeType, $size);

        $this->assertNotNull($result->getHash());
        $this->assertEquals($factory->createHash($data), $result->getHash());
        $this->assertEquals($chunks[0]->getHash(), $result->getChunks()[0]->getHash());
        $this->assertEquals($chunks[0]->getData(), $result->getChunks()[0]->getData());
        $this->assertEquals($chunks[1]->getHash(), $result->getChunks()[1]->getHash());
        $this->assertEquals($chunks[1]->getData(), $result->getChunks()[1]->getData());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
        $this->assertEquals($data, $result->getContent());

        $this->assertTrue($database->contains($result->getChunks()[0]->getHash(), Chunk::class));
        $this->assertTrue($database->contains($result->getChunks()[1]->getHash(), Chunk::class));

        $this->assertEquals(
            $chunks[0]->getData(),
            $database->fetch($chunks[0]->getHash(), Chunk::class)->getData()
        );
        $this->assertEquals(
            $chunks[1]->getData(),
            $database->fetch($chunks[1]->getHash(), Chunk::class)->getData()
        );
    }

    protected function createSearchAdapter()
    {
        $this->searchAdapterMock = $this->prophesize(SearchAdapterInterface::class);

        return $this->searchAdapterMock->reveal();
    }
}
