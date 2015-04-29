<?php

namespace Integration\Component\Access;

use Integration\Parts\BlobFileManagerTrait;
use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Access\FileManager;
use Symcloud\Component\Access\FileManagerInterface;
use Symcloud\Component\MetadataStorage\MetadataManager;
use Symcloud\Component\MetadataStorage\MetadataManagerInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class FileAccessTest extends ProphecyTestCase
{
    use ReferenceManagerTrait;

    /**
     * @var FileManagerInterface
     */
    private $fileManager;

    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var mixed
     */
    private $userProviderMock;

    protected function setUp()
    {
        $this->clearBucket($this->getBlobBucket());
        $this->clearBucket($this->getBlobFileBucket());
        $this->clearBucket($this->getMetadataBucket());

        parent::setUp();
    }

    public function managerProvider()
    {
        $user = $this->prophesize(UserInterface::class);

        return array(
            array($this->getFileManager(), $user->reveal(), '', 0, '', '', '', '')
        );
    }

    /**
     * @dataProvider managerProvider
     *
     * @param FileManagerInterface $fileManager
     * @param UserInterface $user
     * @param string $path
     * @param string $depth
     * @param string $title
     * @param string $description
     * @param string $fileHash
     * @param string $content
     */
    public function testGetByPath(
        FileManagerInterface $fileManager,
        UserInterface $user,
        $path,
        $depth,
        $title,
        $description,
        $fileHash,
        $content
    ) {
        $this->markTestIncomplete(
            'This feature is not fully implemented'
        );

        $result = $fileManager->getByPath($path, $user);

        $this->assertEquals($depth, $result->getDepth());
        $this->assertEquals($path, $result->getPath());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals($fileHash, $result->getFileHash());
        $this->assertEquals($content, $result->getContent());
        $this->assertInstanceOf(KeyValueInterface::class, $result->getMetadataStore());
    }

    protected function getFileManager()
    {
        if (!$this->fileManager) {
            $this->fileManager = new FileManager(
                $this->getReferenceManager(),
                $this->getTreeManager(),
                $this->getBlobFileManager(),
                $this->getMetadataManager(),
                $this->getFactory()
            );
        }

        return $this->fileManager;
    }

    protected function getMetadataManager()
    {
        if (!$this->metadataManager) {
            $this->metadataManager = new MetadataManager();
        }

        return $this->metadataManager;
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
