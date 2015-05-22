<?php

namespace Unit\Component\Reference;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Model\Reference\Reference;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManager;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ReferenceManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    private function getUserProvider()
    {
        $mock = $this->prophesize(UserProviderInterface::class);

        return $mock->reveal();
    }

    public function testFetch()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'HEAD';

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $reference = new Reference();
        $reference->setPolicy(new Policy());
        $reference->setUser($user->reveal());
        $reference->setCommit($commit->reveal());
        $reference->setName($referenceName);

        $database = $this->prophesize(DatabaseInterface::class);
        $database->fetch($referenceName)->willReturn($reference);

        $commitManager = $this->prophesize(CommitManagerInterface::class);
        $commitManager->fetch($commitHash)->willReturn($commit->reveal());

        $manager = new ReferenceManager(
            $database->reveal(),
            $commitManager->reveal(),
            $this->getUserProvider(),
            $factory
        );
        $reference = $manager->fetch($referenceName);

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
    }

    public function testUpdateReference()
    {
        $username = 'johannes';
        $commitHashOld = 'commit-hash-old';
        $commitHashNew = 'commit-hash-new';

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commitOld = $this->prophesize(CommitInterface::class);
        $commitOld->getHash()->willReturn($commitHashOld);

        $commitNew = $this->prophesize(CommitInterface::class);
        $commitNew->getHash()->willReturn($commitHashNew);

        $reference = $this->prophesize(ReferenceInterface::class);
        $reference->update($commitNew->reveal())->shouldBeCalled();

        $database = $this->prophesize(DatabaseInterface::class);
        $database->store($reference->reveal())->shouldBeCalled()->willReturn($reference->reveal());
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager(
            $database->reveal(),
            $commitManager->reveal(),
            $this->getUserProvider(),
            $factory
        );
        $result = $manager->update($reference->reveal(), $commitNew->reveal());

        $this->assertEquals($reference->reveal(), $result);
    }

    public function testCreateReference()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'HEAD';

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $database = $this->prophesize(DatabaseInterface::class);
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager(
            $database->reveal(),
            $commitManager->reveal(),
            $this->getUserProvider(),
            $factory
        );
        $reference = $manager->create($referenceName, $user->reveal(), $commit->reveal());

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
    }

    public function testCreateReferenceWithName()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'NEW-HEAD';

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $database = $this->prophesize(DatabaseInterface::class);
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager(
            $database->reveal(),
            $commitManager->reveal(),
            $this->getUserProvider(),
            $factory
        );
        $reference = $manager->create($referenceName, $user->reveal(), $commit->reveal());

        $this->assertNotNull($reference);
        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
    }
}
