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

use Basho\Riak;
use Basho\Riak\Bucket;
use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceAdapterInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeAdapterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RiakMetadataAdapter extends RiakBaseAdapter implements CommitAdapterInterface, ReferenceAdapterInterface, TreeAdapterInterface
{
    /**
     * @var Bucket
     */
    private $metadataBucket;

    /**
     * RiakMetadataAdapter constructor.
     *
     * @param Riak   $riak
     * @param Bucket $metadataBucket
     */
    public function __construct(Riak $riak, Bucket $metadataBucket)
    {
        parent::__construct($riak);

        $this->metadataBucket = $metadataBucket;
    }

    /**
     * {@inheritdoc}
     */
    public function storeCommit(CommitInterface $commit)
    {
        return $this->storeJson($commit->getHash(), $commit->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function fetchCommitData($hash)
    {
        return $this->fetchJson($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function storeReference(ReferenceInterface $reference)
    {
        return $this->storeJson($reference->getKey(), $reference->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function fetchReferenceData(UserInterface $user, $name = 'HEAD')
    {
        return $this->fetchJson(sprintf('%s-%s', $user->getUsername(), $name));
    }

    /**
     * @param TreeInterface $tree
     *
     * @return bool
     */
    public function storeTree(TreeInterface $tree)
    {
        return $this->storeJson($tree->getHash(), $tree->toArray());
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchTreeData($hash)
    {
        return $this->fetchJson($hash);
    }

    /**
     * @param string $hash
     * @param mixed  $data
     *
     * @return bool
     */
    protected function storeJson($hash, $data)
    {
        return $this->storeObject($hash, $data, $this->metadataBucket)->isSuccess();
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    protected function fetchJson($hash)
    {
        return (array) $this->fetchObject($hash, $this->metadataBucket)->getObject()->getData();
    }
}
