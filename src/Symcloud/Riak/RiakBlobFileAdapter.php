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
use Symcloud\Component\FileStorage\BlobFileAdapterInterface;
use Symcloud\Component\FileStorage\Exception\FileNotFoundException;

class RiakBlobFileAdapter extends RiakBaseAdapter implements BlobFileAdapterInterface
{
    /**
     * @var RiakNamespace
     */
    private $fileNamespace;

    /**
     * RiakBlobFileAdapter constructor.
     *
     * @param RiakClient   $riak
     * @param RiakNamespace $fileNamespace
     */
    public function __construct(RiakClient $riak, RiakNamespace $fileNamespace)
    {
        parent::__construct($riak);

        $this->fileNamespace = $fileNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function storeFile($hash, $blobs)
    {
        $this->storeObject($hash, json_encode($blobs), $this->fileNamespace);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExists($hash)
    {
        $response = $this->fetchObject($hash, $this->fileNamespace);

        return !$response->getNotFound();
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFile($hash)
    {
        $response = $this->fetchObject($hash, $this->fileNamespace);

        if ($response->getNotFound()) {
            throw new FileNotFoundException($hash);
        }

        return json_decode($response->getValue()->getValue());
    }
}
