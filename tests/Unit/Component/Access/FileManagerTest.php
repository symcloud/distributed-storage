<?php

namespace Unit\Component\Access;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Access\FileManager;
use Symcloud\Component\Access\Model\FileModel;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\MetadataManagerInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;
use Symcloud\Component\MetadataStorage\Model\MetadataInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeWalkerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileManagerTest extends ProphecyTestCase
{
    public function testGetFile()
    {
        $parent = '/path/to';
        $name = 'file.sh';
        $path = sprintf('%s/%s', $parent, $name);
        $title = 'My title';
        $description = 'My description';
        $content = 'My content';
        $depth = 3;

        $referenceManager = $this->prophesize(ReferenceManagerInterface::class);
        $treeManager = $this->prophesize(TreeManagerInterface::class);
        $blobFileManager = $this->prophesize(BlobFileManagerInterface::class);
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);

        $user = $this->prophesize(UserInterface::class);
        $reference = $this->prophesize(ReferenceInterface::class);
        $tree = $this->prophesize(TreeInterface::class);
        $commit = $this->prophesize(CommitInterface::class);
        $treeWalker = $this->prophesize(TreeWalkerInterface::class);
        $fileObject = $this->prophesize(FileObjectInterface::class);

        $blobFile = $this->prophesize(BlobFileInterface::class);
        $metadata = $this->prophesize(MetadataInterface::class);
        $keyValue = $this->prophesize(KeyValueInterface::class);

        $referenceManager
            ->getForUser($user->reveal())
            ->willReturn($reference->reveal());

        $reference
            ->getCommit()
            ->willReturn($commit->reveal());

        $commit
            ->getTree()
            ->willReturn($tree->reveal());

        $treeManager
            ->getTreeWalker($tree->reveal())
            ->willReturn($treeWalker->reveal());

        $treeWalker
            ->walk($path)
            ->willReturn($fileObject->reveal());

        $fileObject
            ->isFile()
            ->willReturn(true);

        $factory
            ->createProxy(Argument::is(MetadataInterface::class), Argument::type('callable'))
            ->will(
                function ($args) {
                    return $args[1]();
                }
            );

        $factory
            ->createProxy(Argument::is(BlobFileInterface::class), Argument::type('callable'))
            ->will(
                function ($args) {
                    return $args[1]();
                }
            );

        $blobFileManager
            ->downloadByObject($fileObject->reveal())
            ->willReturn($blobFile->reveal());

        $metadataManager
            ->getByObject($fileObject->reveal())
            ->willReturn($metadata->reveal());

        $factory
            ->createFile($metadata->reveal(), $fileObject->reveal(), $blobFile->reveal())
            ->will(
                function ($args) {
                    $file = new FileModel();
                    $file->setMetadata($args[0]);
                    $file->setObject($args[1]);
                    $file->setData($args[2]);

                    return $file;
                }
            );

        $metadata
            ->getTitle()
            ->willReturn($title);

        $metadata
            ->getDescription()
            ->willReturn($description);

        $fileObject
            ->getDepth()
            ->willReturn($depth);

        $blobFile
            ->getContent(-1, 0)
            ->willReturn($content);

        $fileObject
            ->getParent()
            ->willReturn($parent);

        $fileObject
            ->getName()
            ->willReturn($name);

        $metadata
            ->getKeyValueStore()
            ->willReturn($keyValue->reveal());

        $fileManager = new FileManager(
            $referenceManager->reveal(),
            $treeManager->reveal(),
            $blobFileManager->reveal(),
            $metadataManager->reveal(),
            $factory->reveal()
        );

        $result = $fileManager->getByPath($path, $user->reveal());

        $this->assertEquals($path, $result->getPath());
        $this->assertEquals($depth, $result->getDepth());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals($content, $result->getContent());
        $this->assertInstanceOf(KeyValueInterface::class, $result->getMetadataStore());
    }
}
