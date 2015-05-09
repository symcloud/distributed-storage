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
use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceAdapterInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeAdapterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class RiakMetadataAdapter extends RiakBaseAdapter implements CommitAdapterInterface, ReferenceAdapterInterface, TreeAdapterInterface
{
    /**
     * @var RiakNamespace
     */
    private $metadataNamespace;

    /**
     * RiakMetadataAdapter constructor.
     *
     * @param RiakClient $riak
     * @param RiakNamespace $metadataNamespace
     */
    public function __construct(RiakClient $riak, RiakNamespace $metadataNamespace)
    {
        parent::__construct($riak);

        $this->metadataNamespace = $metadataNamespace;
    }

    /**
     * {@inheritdoc}
     */
    public function storeCommit(CommitInterface $commit)
    {
        $this->storeJson($commit->getHash(), $commit->toArray());
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
        $this->storeJson($reference->getKey(), $reference->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function fetchReferenceData(UserInterface $user, $name = 'HEAD')
    {
        return $this->fetchJson(sprintf('%s-%s', $user->getUsername(), $name));
    }

    /**
     * {@inheritdoc}
     */
    public function storeTree(NodeInterface $tree)
    {
        $this->storeJson($tree->getHash(), $tree->toArray());
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
        $this->storeObject($hash, json_encode($data), $this->metadataNamespace);
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    protected function fetchJson($hash)
    {
        return json_decode($this->fetchObject($hash, $this->metadataNamespace)->getValue()->getValue(), true);
    }
}
