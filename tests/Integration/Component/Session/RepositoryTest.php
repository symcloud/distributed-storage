<?php

namespace Integration\Component\Session;

use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Session\Repository;
use Symcloud\Component\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class RepositoryTest extends ProphecyTestCase
{
    use ReferenceManagerTrait;

    private $userProviderMock;

    public function testLogin()
    {
        $repository = new Repository(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager()
        );

        $user = $this->prophesize(UserInterface::class);

        $session = $repository->login($user->reveal());

        $this->assertInstanceOf(SessionInterface::class, $session);
    }

    public function testLoginWithReference()
    {
        $repository = new Repository(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager()
        );

        $user = $this->prophesize(UserInterface::class);

        $session = $repository->login($user->reveal(), 'MY-HEAD');

        $this->assertInstanceOf(SessionInterface::class, $session);

        $reflectionClass = new \ReflectionClass(get_class($session));
        $reflectionProperty = $reflectionClass->getProperty('referenceName');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('MY-HEAD', $reflectionProperty->getValue($session));
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
