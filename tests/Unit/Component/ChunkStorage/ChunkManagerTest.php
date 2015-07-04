<?php

namespace Unit\Component\ChunkStorage;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\CallPrediction;
use Prophecy\Prediction\NoCallsPrediction;
use Symcloud\Component\ChunkStorage\ChunkManager;
use Symcloud\Component\ChunkStorage\Exception\ChunkNotFoundException;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkInterface;
use Symcloud\Component\Database\Replication\ReplicatorInterface;

class ChunkManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    public function testUpload()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $chunk = new Chunk();
        $chunk->setHash($hash);
        $chunk->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->store(
            Argument::type(ChunkInterface::class),
            array(ReplicatorInterface::OPTION_NAME => ReplicatorInterface::TYPE_FULL)
        )->shouldBeCalled()->willReturn($chunk);

        $database->contains($hash, Chunk::class)->should(new CallPrediction())->willReturn(false);
        $database->fetch($hash, Chunk::class)->should(new NoCallsPrediction());

        $manager = new ChunkManager($factory->reveal(), $database->reveal());

        $result = $manager->upload($data);

        $this->assertEquals($chunk->getHash(), $result->getHash());
        $this->assertEquals($chunk->getData(), $result->getData());
    }

    public function testUploadExists()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $chunk = new Chunk();
        $chunk->setHash($hash);
        $chunk->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->store(Argument::type(ChunkInterface::class))->should(new NoCallsPrediction());
        $database->contains($hash, Chunk::class)->should(new CallPrediction())->willReturn(true);
        $database->fetch($hash, Chunk::class)->should(new NoCallsPrediction());

        $manager = new ChunkManager($factory->reveal(), $database->reveal());

        $result = $manager->upload($data);

        $this->assertEquals($chunk->getHash(), $result->getHash());
        $this->assertEquals($chunk->getData(), $result->getData());
    }

    public function testDownload()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $chunk = new Chunk();
        $chunk->setHash($hash);
        $chunk->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->fetch($hash, Chunk::class)->should(new CallPrediction())->willReturn($chunk);
        $database->contains($hash)->willReturn(true);
        $database->store(Argument::type(ChunkInterface::class))->should(new NoCallsPrediction());

        $manager = new ChunkManager($factory->reveal(), $database->reveal());

        $result = $manager->download($hash);

        $this->assertEquals($chunk->getHash(), $result->getHash());
        $this->assertEquals($chunk->getData(), $result->getData());
    }

    /**
     * @expectedException \Symcloud\Component\ChunkStorage\Exception\ChunkNotFoundException
     */
    public function testDownloadNotExists()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $chunk = new Chunk();
        $chunk->setHash($hash);
        $chunk->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->fetch('my-hash', Chunk::class)->willThrow(new ChunkNotFoundException($hash));
        $database->store(Argument::type(ChunkInterface::class))->should(new NoCallsPrediction());

        $manager = new ChunkManager($factory->reveal(), $database->reveal());

        $manager->download('my-hash');
    }
}
