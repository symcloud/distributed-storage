<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Common;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symcloud\Component\Access\Model\FileModel;
use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\FileStorage\Model\BlobFileModel;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\CommitModel;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceModel;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Model\TreeModel;
use Symfony\Component\Security\Core\User\UserInterface;

class Factory implements FactoryInterface
{
    /**
     * Name of selected hashing algorithm (i.e. "md5", "sha256", "haval160,4", etc..).
     *
     * @var string
     */
    private $algorithm;

    /**
     * Shared secret key used for generating the HMAC variant of the message digest.
     *
     * @var string
     */
    private $key;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $proxyFactory;

    /**
     * Factory constructor.
     *
     * @param string                        $algorithm
     * @param string                        $key
     * @param LazyLoadingValueHolderFactory $proxyFactory
     */
    public function __construct($algorithm, $key, LazyLoadingValueHolderFactory $proxyFactory = null)
    {
        $this->algorithm = $algorithm;
        $this->key = $key;
        $this->proxyFactory = $proxyFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlob($data, $hash = null)
    {
        $blob = new BlobModel();
        $blob->setData($data);
        $blob->setHash($hash !== null ? $hash : $this->createHash($data));

        return $blob;
    }

    /**
     * {@inheritdoc}
     */
    public function createBlobFile($hash, $blobs = array())
    {
        $blobFile = new BlobFileModel();
        $blobFile->setHash($hash);
        $blobFile->setBlobs($blobs);

        return $blobFile;
    }

    /**
     * {@inheritdoc}
     */
    public function createFile(MetadataInterface $metadata, FileObjectInterface $object, BlobFileInterface $blobFile)
    {
        $file = new FileModel();
        $file->setMetadata($metadata);
        $file->setObject($object);
        $file->setData($blobFile);

        return $file;
    }

    /**
     * {@inheritdoc}
     */
    public function createCommit(
        TreeInterface $tree,
        UserInterface $user,
        \DateTime $createdAt,
        $message = '',
        CommitInterface $parentCommit = null,
        $hash = null
    ) {
        $commit = new CommitModel();
        $commit->setTree($tree);
        $commit->setMessage($message);
        if ($parentCommit !== null) {
            $commit->setParentCommit($parentCommit);
        }
        $commit->setCreatedAt($createdAt);
        $commit->setCommitter($user);

        if ($hash === null) {
            $hash = $this->createHash(json_encode($commit->toArray()));
        }
        $commit->setHash($hash);

        return $commit;
    }

    /**
     * {@inheritdoc}
     */
    public function createReference(CommitInterface $commit, UserInterface $user, $name)
    {
        $reference = new ReferenceModel();
        $reference->setCommit($commit);
        $reference->setUser($user);
        $reference->setName($name);

        return $reference;
    }

    /**
     * {@inheritdoc}
     */
    public function createTree($path, TreeInterface $root, $children = array(), $hash = null)
    {
        $tree = new TreeModel();
        $tree->setPath($path);
        $tree->setRoot($root);
        $tree->setChildren($children);
        $tree->setHash($hash);

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function createHash($data)
    {
        return hash_hmac($this->algorithm, $data, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function createFileHash($filePath)
    {
        return hash_hmac_file($this->algorithm, $filePath, $this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function createProxy($className, callable $initializerCallback)
    {
        if ($this->proxyFactory === null) {
            return $initializerCallback();
        }

        return $this->proxyFactory->createProxy(
            $className,
            function (&$wrappedObject, $proxy, $method, $parameters, &$initializer) use ($initializerCallback) {
                $wrappedObject = $initializerCallback($proxy, $method, $parameters);

                $initializer = null;
            }
        );
    }
}
