<?php

namespace Integration\Parts;

use Symcloud\Component\Database\Database;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Metadata\ClassMetadataInterface;
use Symcloud\Component\Database\Metadata\MetadataManager;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Storage\ArrayStorage;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\UserProviderInterface;

trait DatabaseTrait
{
    use FactoryTrait;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * @var UserProviderInterface
     */
    private $userProvider;

    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var StorageAdapterInterface
     */
    private $storageAdapter;

    /**
     * @var SearchAdapterInterface
     */
    private $searchAdapter;

    public function getDatabase()
    {
        if (!$this->database) {
            $this->database = new Database(
                $this->getFactory(),
                $this->getStorageAdapter(),
                $this->getSearchAdapter(),
                $this->getMetadataManager()
            );
        }

        return $this->database;
    }

    protected function getUserProvider()
    {
        if (!$this->userProvider) {
            $this->userProvider = $this->createUserProvider();
        }

        return $this->userProvider;
    }

    protected function getMetadataManager()
    {
        if (!$this->metadataManager) {
            $this->metadataManager = $this->createMetadataManager();
        }

        return $this->metadataManager;
    }

    protected function getStorageAdapter()
    {
        if (!$this->storageAdapter) {
            $this->storageAdapter = $this->createStorageAdapter();
        }

        return $this->storageAdapter;
    }

    protected function getSearchAdapter()
    {
        if (!$this->searchAdapter) {
            $this->searchAdapter = $this->createSearchAdapter();
        }

        return $this->searchAdapter;
    }

    protected function createUserProvider()
    {
        return new InMemoryUserProvider(array('johannes' => array('password' => 'test')));
    }

    protected function createMetadataManager()
    {
        return new MetadataManager($this->getUserProvider());
    }

    protected function createStorageAdapter()
    {
        return new ArrayStorage();
    }

    protected function createSearchAdapter()
    {
        return new NoopSearchAdapter();
    }
}

class NoopSearchAdapter implements SearchAdapterInterface
{
    public function index($hash, ModelInterface $model, ClassMetadataInterface $metadata)
    {
        // TODO: Implement index() method.
    }

    public function search($query, $contexts = array())
    {
        // TODO: Implement search() method.
    }

    public function getStatus()
    {
        // TODO: Implement getStatus() method.
    }

    public function deindex($hash, ClassMetadataInterface $metadata)
    {
        // TODO: Implement deindex() method.
    }

    public function deindexAll()
    {
        // TODO: Implement deindexAll() method.
    }
}
