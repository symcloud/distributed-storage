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

    public function testLogin()
    {
        $repository = new Repository(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager()
        );

        $user = $this->getUserProvider()->loadUserByUsername('johannes');
        $session = $repository->login($user);

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

        $user = $this->getUserProvider()->loadUserByUsername('johannes');
        $session = $repository->login($user, 'MY-HEAD');

        $this->assertInstanceOf(SessionInterface::class, $session);

        $reflectionClass = new \ReflectionClass(get_class($session));
        $reflectionProperty = $reflectionClass->getProperty('referenceName');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('MY-HEAD', $reflectionProperty->getValue($session));
    }
}
