<?php

namespace Integration\Parts;

use Symcloud\Component\Database\Database;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Metadata\ClassMetadataInterface;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Search\ZendLuceneAdapter;
use Symcloud\Component\Database\Storage\ArrayStorage;
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

    public function getDatabase()
    {
        if (!$this->database) {
            $this->database = new Database(
                $this->getFactory(),
                new ArrayStorage(),
                new NoopSearchAdapter(),
                $this->getUserProvider()
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

    protected function createUserProvider()
    {
        return new InMemoryUserProvider(array('johannes' => array('password' => 'test')));
    }
}

class NoopSearchAdapter implements SearchAdapterInterface
{
    public function index($hash, ModelInterface $model, ClassMetadataInterface $metadata)
    {
        // TODO: Implement index() method.
    }

    public function search($query, $contexts = null)
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
