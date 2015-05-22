<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\BlobStorage;

use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobInterface;
use Symcloud\Component\Database\Model\Policy;

class BlobManager implements BlobManagerInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var DatabaseInterface
     */
    private $database;

    /**
     * BlobManager constructor.
     *
     * @param FactoryInterface $factory
     * @param DatabaseInterface $database
     */
    public function __construct(FactoryInterface $factory, DatabaseInterface $database)
    {
        $this->factory = $factory;
        $this->database = $database;
    }

    /**
     * @param $data
     *
     * @return BlobInterface
     */
    public function upload($data)
    {
        $blob = new Blob();
        $blob->setData($data);
        $blob->setLength(strlen($data));
        $blob->setPolicy(new Policy());
        $blob->setHash($this->factory->createHash($data));

        if ($this->database->contains($blob->getHash())) {
            return $blob;
        }

        return $this->database->store($blob);
    }

    /**
     * @param string $hash
     *
     * @return BlobInterface
     */
    public function download($hash)
    {
        return $this->database->fetch($hash, Blob::class);
    }

    public function downloadProxy($hash)
    {
        return $this->factory->createProxy(
            BlobInterface::class,
            function () use ($hash) {
                return $this->download($hash);
            }
        );
    }
}
