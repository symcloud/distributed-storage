<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Riak;

use Riak\Client\Core\Query\RiakNamespace;
use Riak\Client\RiakClient;
use Symcloud\Component\BlobStorage\BlobAdapterInterface;
use Symcloud\Component\BlobStorage\Exception\BlobNotFoundException;

class RiakBlobAdapter extends RiakBaseAdapter implements BlobAdapterInterface
{
    /**
     * @var RiakNamespace
     */
    private $blobNamespace;

    /**
     * RiakBlobAdapter constructor.
     *
     * @param RiakClient $riak
     * @param RiakNamespace $blobNamespace
     */
    public function __construct(RiakClient $riak, RiakNamespace $blobNamespace)
    {
        parent::__construct($riak);

        $this->blobNamespace = $blobNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function storeBlob($hash, $data)
    {
        $this->storeObject($hash, $data, $this->blobNamespace);
    }

    /**
     * {@inheritdoc}
     */
    public function blobExists($hash)
    {
        $response = $this->fetchObject($hash, $this->blobNamespace);

        return !$response->getNotFound();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchBlob($hash)
    {
        $response = $this->fetchObject($hash, $this->blobNamespace);

        if ($response->getNotFound()) {
            throw new BlobNotFoundException($hash);
        }

        return $response->getValue()->getValue()->getContents();
    }
}
