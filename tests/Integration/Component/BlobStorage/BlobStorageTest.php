<?php

namespace Integration\Component\BlobStorage;

use Integration\Parts\BlobManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\Policy;

class BlobStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobManagerTrait;

    public function storageProvider()
    {
        $length = 200;
        $data = $this->generateString($length);

        $expectedBlob = new Blob();
        $expectedBlob->setData($data);
        $expectedBlob->setHash($this->getFactory()->createHash($data));
        $expectedBlob->setLength($length);
        $expectedBlob->setPolicy(new Policy());

        $this->getDatabase()->deleteAll();

        return array(
            array($expectedBlob)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobInterface $expectedBlob
     */
    public function testUpload(
        BlobInterface $expectedBlob
    ) {
        $blobManager = $this->getBlobManager();
        $blob = $blobManager->upload($expectedBlob->getData());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());
        $this->assertEquals($expectedBlob->getLength(), $blob->getLength());

        $database = $this->getDatabase();
        $data = $database->fetch($expectedBlob->getHash(), Blob::class);

        $this->assertEquals($expectedBlob->getHash(), $data->getHash());
        $this->assertEquals($expectedBlob->getData(), $data->getData());
        $this->assertEquals($expectedBlob->getLength(), $data->getLength());
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobInterface $expectedBlob
     */
    public function testDownload(
        BlobInterface $expectedBlob
    ) {
        $blobManager = $this->getBlobManager();
        $database = $this->getDatabase();
        $database->store($expectedBlob);

        $blob = $blobManager->download($expectedBlob->getHash());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());
        $this->assertEquals($expectedBlob->getLength(), $blob->getLength());
    }
}
