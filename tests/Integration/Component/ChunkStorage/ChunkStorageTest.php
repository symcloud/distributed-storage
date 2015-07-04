<?php

namespace Integration\Component\ChunkStorage;

use Integration\Parts\ChunkManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkInterface;
use Symcloud\Component\Database\Model\PolicyCollection;

class ChunkStorageTest extends ProphecyTestCase
{
    use TestFileTrait, ChunkManagerTrait;

    public function storageProvider()
    {
        $length = 200;
        $data = $this->generateString($length);

        $expectedChunk = new Chunk();
        $expectedChunk->setData($data);
        $expectedChunk->setHash($this->getFactory()->createHash($data));
        $expectedChunk->setLength($length);
        $expectedChunk->setPolicyCollection(new PolicyCollection());

        $this->getDatabase()->deleteAll();

        return array(
            array($expectedChunk)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param ChunkInterface $expectedChunk
     */
    public function testUpload(ChunkInterface $expectedChunk) {
        $manager = $this->getChunkManager();
        $chunk = $manager->upload($expectedChunk->getData());

        $this->assertEquals($expectedChunk->getHash(), $chunk->getHash());
        $this->assertEquals($expectedChunk->getData(), $chunk->getData());
        $this->assertEquals($expectedChunk->getLength(), $chunk->getLength());

        $database = $this->getDatabase();
        $data = $database->fetch($expectedChunk->getHash(), Chunk::class);

        $this->assertEquals($expectedChunk->getHash(), $data->getHash());
        $this->assertEquals($expectedChunk->getData(), $data->getData());
        $this->assertEquals($expectedChunk->getLength(), $data->getLength());
    }

    /**
     * @dataProvider storageProvider
     *
     * @param ChunkInterface $expectedChunk
     */
    public function testDownload(ChunkInterface $expectedChunk) {
        $manager = $this->getChunkManager();
        $database = $this->getDatabase();
        $database->store($expectedChunk);

        $chunk = $manager->download($expectedChunk->getHash());

        $this->assertEquals($expectedChunk->getHash(), $chunk->getHash());
        $this->assertEquals($expectedChunk->getData(), $chunk->getData());
        $this->assertEquals($expectedChunk->getLength(), $chunk->getLength());
    }
}
