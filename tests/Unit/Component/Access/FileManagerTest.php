<?php

namespace Unit\Component\Access;

use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Access\FileManager;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\FileStorage\BlobFileManagerInterface;
use Symcloud\Component\MetadataStorage\MetadataManagerInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileManagerTest extends ProphecyTestCase
{
    public function testGetFile()
    {
        $referenceManager = $this->prophesize(ReferenceManagerInterface::class);
        $treeManager = $this->prophesize(TreeManagerInterface::class);
        $blobFileManager = $this->prophesize(BlobFileManagerInterface::class);
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $factory = $this->prophesize(FactoryInterface::class);
        $user = $this->prophesize(UserInterface::class);

        $fileManager = new FileManager(
            $referenceManager->reveal(),
            $treeManager->reveal(),
            $blobFileManager->reveal(),
            $metadataManager->reveal(),
            $factory->reveal()
        );

        $result = $fileManager->getByPath('/path/to/file.sh', $user->reveal());

        $this->assertEquals('/path/to/file.sh', $result->getPath());
        $this->assertEquals(3, $result->getDepth());
        $this->assertEquals('My title', $result->getTitle());
        $this->assertEquals('My description', $result->getDescription());
        $this->assertEquals('My Content', $result->getContent());
        $this->assertInstanceOf(KeyValueInterface::class, $result->getMetadataStore());
    }
}
