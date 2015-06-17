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

    public function testLoginByHash()
    {
        $repository = new Repository(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager()
        );

        $user = $this->getUserProvider()->loadUserByUsername('johannes');
        $session = $repository->loginByHash($user, '123-123-123');

        $this->assertInstanceOf(SessionInterface::class, $session);

        $reflectionClass = new \ReflectionClass(get_class($session));
        $reflectionProperty = $reflectionClass->getProperty('referenceHash');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('123-123-123', $reflectionProperty->getValue($session));
    }

    public function testLoginByName()
    {
        $repository = new Repository(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager()
        );

        $user = $this->getUserProvider()->loadUserByUsername('johannes');
        $session = $repository->loginByName($user, 'MY-HEAD');

        $this->assertInstanceOf(SessionInterface::class, $session);

        $reflectionClass = new \ReflectionClass(get_class($session));
        $reflectionProperty = $reflectionClass->getProperty('referenceName');
        $reflectionProperty->setAccessible(true);

        $this->assertEquals('MY-HEAD', $reflectionProperty->getValue($session));
    }
}
