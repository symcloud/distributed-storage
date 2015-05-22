<?php

namespace Unit\Component\BlobStorage;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\CallPrediction;
use Prophecy\Prediction\NoCallsPrediction;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobInterface;

class BlobManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    public function testUpload()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $blob = new Blob();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->store(Argument::type(BlobInterface::class))->should(new CallPrediction())->willReturn($blob);
        $database->contains($hash)->should(new CallPrediction())->willReturn(false);
        $database->fetch($hash, Blob::class)->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $database->reveal());

        $result = $manager->upload($data);

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    public function testUploadExists()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $blob = new Blob();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->store(Argument::type(BlobInterface::class))->should(new NoCallsPrediction());
        $database->contains($hash)->should(new CallPrediction())->willReturn(true);
        $database->fetch($hash, Blob::class)->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $database->reveal());

        $result = $manager->upload($data);

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    public function testDownload()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $blob = new Blob();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->fetch($hash, Blob::class)->should(new CallPrediction())->willReturn($blob);
        $database->contains($hash)->willReturn(true);
        $database->store(Argument::type(BlobInterface::class))->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $database->reveal());

        $result = $manager->download($hash);

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    /**
     * @expectedException \Symcloud\Component\BlobStorage\Exception\BlobNotFoundException
     */
    public function testDownloadNotExists()
    {
        $data = 'This is my data';
        $hash = $this->getFactory()->createHash($data);

        $blob = new Blob();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $factory->createHash($data)->willReturn($hash);

        $database = $this->prophesize(DatabaseInterface::class);

        $database->fetch('my-hash', Blob::class)->willThrow(new BlobNotFoundException($hash));
        $database->store(Argument::type(BlobInterface::class))->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $database->reveal());

        $manager->download('my-hash');
    }
}
