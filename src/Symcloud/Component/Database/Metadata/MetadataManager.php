<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata;

use Symcloud\Component\Database\Metadata\ClassMetadata\ChunkClassMetadata;
use Symcloud\Component\Database\Metadata\ClassMetadata\ChunkFileClassMetadata;
use Symcloud\Component\Database\Metadata\ClassMetadata\CommitClassMetadata;
use Symcloud\Component\Database\Metadata\ClassMetadata\ReferenceClassMetadata;
use Symcloud\Component\Database\Metadata\ClassMetadata\TreeClassMetadata;
use Symcloud\Component\Database\Metadata\ClassMetadata\TreeFileClassMetadata;
use Symcloud\Component\Database\Model\Chunk;
use Symcloud\Component\Database\Model\ChunkFile;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Model\Reference\Reference;
use Symcloud\Component\Database\Model\Tree\Tree;
use Symcloud\Component\Database\Model\Tree\TreeFile;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MetadataManager implements MetadataManagerInterface
{
    /**
     * @var ClassMetadataInterface[]
     */
    public $metadata;

    /**
     * MetadataManager constructor.
     *
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        $this->metadata = array(
            Chunk::class => new ChunkClassMetadata(),
            ChunkFile::class => new ChunkFileClassMetadata(),
            Tree::class => new TreeClassMetadata(),
            TreeFile::class => new TreeFileClassMetadata(),
            Commit::class => new CommitClassMetadata($userProvider),
            Reference::class => new ReferenceClassMetadata($userProvider),
        );
    }

    public function loadByModel(ModelInterface $object)
    {
        return $this->loadByClassname($object->getClass());
    }

    public function loadByClassname($className)
    {
        return $this->metadata[$className];
    }
}
