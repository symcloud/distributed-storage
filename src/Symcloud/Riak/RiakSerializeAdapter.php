<?php

namespace Symcloud\Riak;

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;

class RiakSerializeAdapter extends RiakBaseAdapter implements CommitAdapterInterface
{
    /**
     * @var Bucket
     */
    private $metadataBucket;

    /**
     * RiakSerializeAdapter constructor.
     * @param Riak $riak
     * @param Bucket $metadataBucket
     */
    public function __construct(Riak $riak, Bucket $metadataBucket)
    {
        parent::__construct($riak);

        $this->metadataBucket = $metadataBucket;
    }

    /**
     * @param CommitInterface $commit
     * @return boolean
     */
    public function storeCommit(CommitInterface $commit)
    {
        return $this->storeJson($commit->getHash(), $commit);
    }

    /**
     * @param string $hash
     * @return array
     */
    public function fetchCommit($hash)
    {
        return $this->fetchJson($hash);
    }

    /**
     * @param string $hash
     * @param mixed $data
     * @return boolean
     */
    protected function storeJson($hash, $data)
    {
        return $this->storeObject($hash, json_encode($data), $this->metadataBucket)->isSuccess();
    }

    /**
     * @param string $hash
     * @return array
     */
    protected function fetchJson($hash)
    {
        return json_decode($this->fetchObject($hash, $this->metadataBucket)->getObject()->getData(), true);
    }
}
