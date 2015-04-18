<?php

namespace Unit\Component\BlobStorage;

use Prophecy\PhpUnit\ProphecyTestCase;
use Prophecy\Prediction\CallPrediction;
use Prophecy\Prediction\NoCallsPrediction;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\BlobManager;
use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\Common\FactoryInterface;

class BlobManagerTest extends ProphecyTestCase
{
    public function testUpload()
    {
        $data = 'This is my data';
        $hash = 'my-hash';

        $blob = new BlobModel();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobAdapterInterface::class);

        $factory->createBlob($data)->willReturn($blob);

        $adapter->blobExists($hash)->willReturn(false);
        $adapter->storeBlob($hash, $data)->should(new CallPrediction());
        $adapter->fetchBlob($hash)->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $adapter->reveal());

        $result = $manager->uploadBlob($data);

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    public function testUploadExists()
    {
        $data = 'This is my data';
        $hash = 'my-hash';

        $blob = new BlobModel();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobAdapterInterface::class);

        $factory->createBlob($data)->willReturn($blob);

        $adapter->blobExists($hash)->willReturn(true);
        $adapter->storeBlob($hash, $data)->should(new NoCallsPrediction());
        $adapter->fetchBlob($hash)->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $adapter->reveal());

        $result = $manager->uploadBlob($data);

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }

    public function testDownload()
    {
        $data = 'This is my data';
        $hash = 'my-hash';

        $blob = new BlobModel();
        $blob->setHash($hash);
        $blob->setData($data);

        $factory = $this->prophesize(FactoryInterface::class);
        $adapter = $this->prophesize(BlobAdapterInterface::class);

        $factory->createBlob($data,$hash)->should(new CallPrediction())->willReturn($blob);

        $adapter->fetchBlob($hash)->should(new CallPrediction())->willReturn($data);
        $adapter->storeBlob($hash)->should(new NoCallsPrediction());
        $adapter->blobExists($hash)->should(new NoCallsPrediction());

        $manager = new BlobManager($factory->reveal(), $adapter->reveal());

        $result = $manager->downloadBlob('my-hash');

        $this->assertEquals($blob->getHash(), $result->getHash());
        $this->assertEquals($blob->getData(), $result->getData());
    }
}
