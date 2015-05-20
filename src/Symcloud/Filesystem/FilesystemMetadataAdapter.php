<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Filesystem;

use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceAdapterInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeAdapterInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FilesystemMetadataAdapter extends FilesystemBaseAdapter implements CommitAdapterInterface, ReferenceAdapterInterface, TreeAdapterInterface
{
    const FILE_EXTENSION = '.symcloud.metadata.json';

    /**
     * FilesystemBlobAdapter constructor.
     *
     * @param string $directory
     */
    public function __construct($directory)
    {
        parent::__construct($directory, self::FILE_EXTENSION);
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
        return $this->fetchReferenceDataByUsername($user->getUsername(), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchReferenceDataByUsername($username, $name = 'HEAD')
    {
        return $this->fetchJson(sprintf('%s-%s', $username, $name));
    }

    /**
     * {@inheritdoc}
     */
    public function storeTree(NodeInterface $tree)
    {
        $hash = $tree->getHash();
        $data = $tree->toArray();

        return $this->storeJson($hash, $data);
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    public function fetchTreeData($hash)
    {
        return array_merge(array(self::HASH_KEY => $hash), $this->fetchJson($hash));
    }

    /**
     * @param string $hash
     * @param mixed $data
     *
     * @return bool
     */
    protected function storeJson($hash, $data)
    {
        return $this->save($hash, json_encode($data));
    }

    /**
     * @param string $hash
     *
     * @return array
     */
    protected function fetchJson($hash)
    {
        return json_decode($content = $this->fetch($hash), true);
    }
}
