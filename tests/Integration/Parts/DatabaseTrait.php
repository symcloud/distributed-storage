<?php

namespace Integration\Parts;

use Symcloud\Component\Database\Database;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Search\ZendLuceneAdapter;
use Symcloud\Component\Database\Storage\ArrayStorage;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\User\User;
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
            $tempName = sys_get_temp_dir();

            $this->database = new Database(
                $this->getFactory(),
                new ArrayStorage(),
                new ZendLuceneAdapter($tempName),
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
