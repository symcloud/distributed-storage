<?php

namespace Symcloud\Component\Access;

use Symcloud\Component\Access\Exception\NotAFileException;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\MetadataManagerInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileManager implements FileManagerInterface
{
    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var BlobFileManagerInterface
     */
    private $blobFileManager;

    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * FileManager constructor.
     * @param ReferenceManagerInterface $referenceManager
     * @param TreeManagerInterface $treeManager
     * @param BlobFileManagerInterface $blobFileManager
     * @param MetadataManagerInterface $metadataManager
     * @param FactoryInterface $factory
     */
    public function __construct(
        ReferenceManagerInterface $referenceManager,
        TreeManagerInterface $treeManager,
        BlobFileManagerInterface $blobFileManager,
        MetadataManagerInterface $metadataManager,
        FactoryInterface $factory
    ) {
        $this->referenceManager = $referenceManager;
        $this->treeManager = $treeManager;
        $this->blobFileManager = $blobFileManager;
        $this->metadataManager = $metadataManager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function getByPath($path, UserInterface $user)
    {
        $reference = $this->referenceManager->getForUser($user);
        $commit = $reference->getCommit();
        $tree = $commit->getTree();

        $treeWalker = $this->treeManager->getTreeWalker($tree);
        $object = $treeWalker->walk($path);

        // TODO security-checker for object

        if (!$object->isFile()) {
            throw new NotAFileException($path);
        }

        $blobFile = $this->factory->createProxy(
            BlobFileInterface::class,
            function () use ($object) {
                return $this->blobFileManager->downloadByObject($object);
            }
        );

        $metadata = $this->factory->createProxy(
            MetadataInterface::class,
            function () use ($object) {
                return $this->metadataManager->getByObject($object);
            }
        );

        return $this->factory->createFile($metadata, $object, $blobFile);
    }
}
