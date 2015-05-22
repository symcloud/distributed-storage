<?php

namespace Integration\Component\Database\StorageAdapter;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Storage\ArrayStorage;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;

class StorageAdapterTest extends ProphecyTestCase
{
    public function adapterProvider()
    {
        $hash = 'my-hash';
        $data = array(
            'my' => 'data'
        );

        return array(
            array(new ArrayStorage(), $hash, $data),
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testStore(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testStoreTwice(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
        $storageAdapter->store($hash, $data);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testFetch(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
        $result = $storageAdapter->fetch($hash);

        $this->assertEquals($data, $result);
    }

    /**
     * @dataProvider adapterProvider
     * @expectedException \Exception
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testFetchNotExists(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->fetch($hash);
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testContains(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
        $this->assertTrue($storageAdapter->contains($hash));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testContainsNotExists(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $this->assertFalse($storageAdapter->contains($hash));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testDelete(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
        $this->assertTrue($storageAdapter->contains($hash));

        $storageAdapter->delete($hash);
        $this->assertFalse($storageAdapter->contains($hash));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testDeleteNotExists(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->delete($hash);
        $this->assertFalse($storageAdapter->contains($hash));
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param StorageAdapterInterface $storageAdapter
     * @param string $hash
     * @param array $data
     */
    public function testDeleteAll(StorageAdapterInterface $storageAdapter, $hash, $data)
    {
        $storageAdapter->store($hash, $data);
        $storageAdapter->store('twice', array());

        $storageAdapter->deleteAll();
        $this->assertFalse($storageAdapter->contains($hash));
        $this->assertFalse($storageAdapter->contains('twice'));
    }
}
