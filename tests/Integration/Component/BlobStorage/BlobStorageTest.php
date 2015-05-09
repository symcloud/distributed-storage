<?php

namespace Integration\Component\BlobStorage;

use Integration\Parts\BlobManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\BlobStorage\BlobManagerInterface;
use Symcloud\Component\BlobStorage\Model\BlobInterface;

class BlobStorageTest extends ProphecyTestCase
{
    use TestFileTrait, BlobManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobNamespace());

        parent::setUp();
    }

    public function storageProvider()
    {
        $length = 200;
        $data = $this->generateString($length);

        $expectedBlob = $this->getFactory()->createBlob($data);

        $blobManager = $this->getBlobManager();
        $blobNamespace = $this->getBlobNamespace();

        return array(
            array($blobManager, $expectedBlob, $blobNamespace)
        );
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobManager
     * @param BlobInterface $expectedBlob
     * @param RiakNamespace $blobNamespace
     */
    public function testUpload(
        BlobManagerInterface $blobManager,
        BlobInterface $expectedBlob,
        RiakNamespace $blobNamespace
    ) {
        $blob = $blobManager->uploadBlob($expectedBlob->getData());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $riakResponse = $this->fetchObject($blob->getHash(), $blobNamespace);
        $this->assertEquals($expectedBlob->getData(), $riakResponse->getValue()->getValue()->getContents());

        $blobKeys = $this->fetchBucketKeys($blobNamespace);
        $this->assertContains($blob->getHash(), $blobKeys);

        $riakResponse = $this->fetchObject($blob->getHash(), $blobNamespace);
        $this->assertEquals($expectedBlob->getData(), $riakResponse->getValue()->getValue()->getContents());
    }

    /**
     * @dataProvider storageProvider
     *
     * @param BlobManagerInterface $blobManager
     * @param BlobInterface $expectedBlob
     * @param RiakNamespace $blobNamespace
     */
    public function testDownload(
        BlobManagerInterface $blobManager,
        BlobInterface $expectedBlob,
        RiakNamespace $blobNamespace
    ) {
        $this->storeObject($expectedBlob->getHash(), $expectedBlob->getData(), $blobNamespace);

        $blob = $blobManager->downloadBlob($expectedBlob->getHash());

        $this->assertEquals($expectedBlob->getHash(), $blob->getHash());
        $this->assertEquals($expectedBlob->getData(), $blob->getData());

        $blobKeys = $this->fetchBucketKeys($blobNamespace);
        $this->assertContains($blob->getHash(), $blobKeys);
    }
}
