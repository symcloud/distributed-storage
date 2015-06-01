<?php

namespace Integration\Component\Database\StorageAdapter;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Storage\ArrayStorage;
use Symcloud\Component\Database\Storage\FilesystemStorage;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;
use Symfony\Component\Filesystem\Filesystem;

class StorageAdapterTest extends ProphecyTestCase
{
    public function adapterProvider()
    {
        $hash = 'my-hash';
        $data = array(
            'my' => 'data'
        );

        $arrayStorage = new ArrayStorage();
        $filesystemStorage = new FilesystemStorage(__DIR__ . '/database', new Filesystem());
        $filesystemStorage->deleteAll();

        $context = 'test-context';

        return array(
            array($arrayStorage, $hash, $data, $context),
            array($filesystemStorage, $hash, $data, $context),
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testStore(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testStoreTwice(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testFetch(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $result = $storageAdapter->fetch($hash, $context);

        $this->assertEquals($data, $result);
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Exception
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testFetchNotExists(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $storageAdapter->fetch($hash, $context);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testContains(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $this->assertTrue($storageAdapter->contains($hash, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testContainsNotExists(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertFalse($storageAdapter->contains($hash, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testDelete(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $this->assertTrue($storageAdapter->contains($hash, $context));

        $this->assertTrue($storageAdapter->delete($hash, $context));
        $this->assertFalse($storageAdapter->contains($hash, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testDeleteNotExists(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $this->assertFalse($storageAdapter->delete($hash, $context));
        $this->assertFalse($storageAdapter->contains($hash, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testDeleteAll(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $hash2 = 'hash2';
        $data2 = 'data2';

        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $this->assertTrue($storageAdapter->store($hash2, $data2, $context));

        $this->assertTrue($storageAdapter->deleteAll($context));
        $this->assertFalse($storageAdapter->contains($hash, $context));
        $this->assertFalse($storageAdapter->contains($hash2, $context));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     * @param string $context
     */
    public function testDeleteAllWithoutContext(StorageAdapterInterface $storageAdapter, $hash, $data, $context)
    {
        $hash2 = 'hash2';
        $data2 = 'data2';
        $context2 = 'context2';

        $this->assertTrue($storageAdapter->store($hash, $data, $context));
        $this->assertTrue($storageAdapter->store($hash2, $data2, $context2));

        $this->assertTrue($storageAdapter->deleteAll());
        $this->assertFalse($storageAdapter->contains($hash, $context));
        $this->assertFalse($storageAdapter->contains($hash2, $context2));
    }

    protected function tearDown()
    {
        $filesystem = new Filesystem();
        if (is_dir(__DIR__ . '/database')) {
            $filesystem->remove(__DIR__ . '/database');
        }

        parent::tearDown();
    }
}
