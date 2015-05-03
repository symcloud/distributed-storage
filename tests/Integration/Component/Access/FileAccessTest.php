<?php

namespace Integration\Component\Access;

use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Access\FileManager;
use Symcloud\Component\Access\FileManagerInterface;
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
    }

    protected function getFileManager()
    {
        if (!$this->fileManager) {
            $this->fileManager = new FileManager(
                $this->getReferenceManager(),
                $this->getTreeManager(),
                $this->getFactory()
            );
        }

        return $this->fileManager;
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
