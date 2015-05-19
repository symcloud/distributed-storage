<?php

namespace Integration\Riak;

use Integration\Parts\BlobFileManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;

class RiakBlobFileAdapterTest extends ProphecyTestCase
{
    use BlobFileManagerTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobFileNamespace());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getBlobFileAdapter(), $this->getBlobFileNamespace(), $this->getFactory())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreFile(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile('my-hash', array($blob1->reveal(), $blob2->reveal()), 'application/json', 999);
        $adapter->storeFile($file->getHash(), $file->toArray());

        $response = $this->fetchObject($file->getHash(), $blobFileNamespace);
        $this->assertFalse($response->getNotFound());
        $this->assertEquals(
            array(
                BlobFileInterface::MIME_TYPE_KEY => 'application/json',
                BlobFileInterface::SIZE_KEY => 999,
                BlobFileInterface::BLOBS_KEY => array('hash1', 'hash2'),
            ),
            json_decode($response->getValue()->getValue()->getContents(), true)
        );

        $keys = $this->fetchBucketKeys($blobFileNamespace);
        $this->assertContains($file->getHash(), $keys);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testStoreFileAlreadyExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile('my-hash', array($blob1->reveal(), $blob2->reveal()), 'application/json', 999);
        $this->storeObject($file->getHash(), $file->toArray(), $blobFileNamespace);

        // no exception expected
        $adapter->storeFile($file->getHash(), $file->toArray());
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Symcloud\Component\FileStorage\Exception\FileNotFoundException
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchFileNotExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile(
            'my-not-existing-hash',
            array($blob1->reveal(), $blob2->reveal()),
            'application/json',
            999
        );
        $adapter->fetchFile($file->getHash());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFetchFile(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile('my-hash', array($blob1->reveal(), $blob2->reveal()), 'application/json', 999);
        $this->storeObject($file->getHash(), $file->toArray(), $blobFileNamespace);

        $result = $adapter->fetchFile($file->getHash());

        $this->assertEquals($file->toArray(), $result);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFileExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile('my-hash', array($blob1->reveal(), $blob2->reveal()), 'application/json', 999);
        $this->storeObject($file->getHash(), $file->toArray(), $blobFileNamespace);

        $this->assertTrue($adapter->fileExists($file->getHash()));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param BlobFileAdapterInterface $adapter
     * @param RiakNamespace $blobFileNamespace
     * @param FactoryInterface $factory
     */
    public function testFileNotExists(
        BlobFileAdapterInterface $adapter,
        RiakNamespace $blobFileNamespace,
        FactoryInterface $factory
    ) {
        $blob1 = $this->prophesize(BlobInterface::class);
        $blob1->getHash()->willReturn('hash1');
        $blob2 = $this->prophesize(BlobInterface::class);
        $blob2->getHash()->willReturn('hash2');

        $file = $factory->createBlobFile(
            'my-not-existing-hash',
            array($blob1->reveal(), $blob2->reveal()),
            'application/json',
            999
        );

        $this->assertFalse($adapter->fileExists($file->getHash()));
    }
}
