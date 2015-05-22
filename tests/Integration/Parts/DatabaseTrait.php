<?php

namespace Integration\Parts;

use Symcloud\Component\Database\Database;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Search\ZendLuceneAdapter;
use Symcloud\Component\Database\Storage\ArrayStorage;

trait DatabaseTrait
{
    use FactoryTrait;

    /**
     * @var DatabaseInterface
     */
    private $database;

    public function getDatabase()
    {
        if (!$this->database) {
            $tempName = sys_get_temp_dir();

            $this->database = new Database(
                $this->getFactory(),
                new ArrayStorage(),
                new ZendLuceneAdapter($tempName)
            );
        }

        return $this->database;
    }
}
