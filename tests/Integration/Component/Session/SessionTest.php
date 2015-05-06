<?php

namespace Integration\Component\Session;

use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionTest extends ProphecyTestCase
{
    use ReferenceManagerTrait;

    /**
     * @var mixed
     */
    private $userProviderMock;

    protected function setUp()
    {
        parent::setUp();

        $this->clearBucket($this->getMetadataBucket());
        $this->clearBucket($this->getBlobFileBucket());
        $this->clearBucket($this->getBlobBucket());
    }

    public function testInit()
    {
        $userMock = $this->prophesize(UserInterface::class);
        $user = $userMock->reveal();

        $session = new Session(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager(),
            'HEAD',
            $user
        );

        $session->init();
        $root = $session->getRoot();

        $this->assertInstanceOf(TreeInterface::class, $root);
        $this->assertEquals('/', $root->getPath());
        $this->assertEquals(array(), $root->getChildren());
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
