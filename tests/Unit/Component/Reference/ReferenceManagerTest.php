<?php

namespace Unit\Component\Reference;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceAdapterInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManager;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferenceManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    public function testGetForUserReference()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'HEAD';
        $referenceKey = sprintf('%s/%s', $username, $referenceName);
        $data = array(
            ReferenceInterface::USER_KEY => $username,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::NAME_KEY => $referenceName,
        );

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $referenceAdapter = $this->prophesize(ReferenceAdapterInterface::class);
        $referenceAdapter->fetchReference($user->reveal(), $referenceName)->willReturn($data);

        $commitManager = $this->prophesize(CommitManagerInterface::class);
        $commitManager->fetchProxy($commitHash)->willReturn($commit->reveal());

        $manager = new ReferenceManager($referenceAdapter->reveal(), $commitManager->reveal(), $factory);
        $reference = $manager->getForUser($user->reveal());

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($referenceKey, $reference->getKey());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
        $this->assertEquals($data, $reference->toArray());
    }

    public function testGetForUserReferenceWithName()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'HEAD-NEW';
        $referenceKey = sprintf('%s/%s', $username, $referenceName);
        $data = array(
            ReferenceInterface::USER_KEY => $username,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::NAME_KEY => $referenceName,
        );

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $referenceAdapter = $this->prophesize(ReferenceAdapterInterface::class);
        $referenceAdapter->fetchReference($user->reveal(), $referenceName)->willReturn($data);

        $commitManager = $this->prophesize(CommitManagerInterface::class);
        $commitManager->fetchProxy($commitHash)->willReturn($commit->reveal());

        $manager = new ReferenceManager($referenceAdapter->reveal(), $commitManager->reveal(), $factory);
        $reference = $manager->getForUser($user->reveal(), $referenceName);

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($referenceKey, $reference->getKey());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
        $this->assertEquals($data, $reference->toArray());
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

        $referenceAdapter = $this->prophesize(ReferenceAdapterInterface::class);
        $referenceAdapter->storeReference($reference->reveal())->shouldBeCalled()->willReturn(true);
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager($referenceAdapter->reveal(), $commitManager->reveal(), $factory);
        $this->assertTrue($manager->update($reference->reveal(), $commitNew->reveal()));
    }

    public function testCreateReference()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'HEAD';
        $referenceKey = sprintf('%s/%s', $username, $referenceName);
        $data = array(
            ReferenceInterface::USER_KEY => $username,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::NAME_KEY => $referenceName,
        );

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $assertEquals = array($this, 'assertEquals');

        $referenceAdapter = $this->prophesize(ReferenceAdapterInterface::class);
        $referenceAdapter->storeReference(Argument::type(ReferenceInterface::class))->shouldBeCalled()->will(
            function ($args) use ($assertEquals, $data, $referenceKey) {
                call_user_func_array($assertEquals, array($referenceKey, $args[0]->getKey()));
                call_user_func_array($assertEquals, array($data, $args[0]->toArray()));
            }
        );
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager($referenceAdapter->reveal(), $commitManager->reveal(), $factory);
        $reference = $manager->create($user->reveal(), $commit->reveal());

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($referenceKey, $reference->getKey());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
        $this->assertEquals($data, $reference->toArray());
    }

    public function testCreateReferenceWithName()
    {
        $username = 'johannes';
        $commitHash = 'commit-hash';
        $referenceName = 'NEW-HEAD';
        $referenceKey = sprintf('%s/%s', $username, $referenceName);
        $data = array(
            ReferenceInterface::USER_KEY => $username,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::NAME_KEY => $referenceName,
        );

        $factory = $this->getFactory();

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $assertEquals = array($this, 'assertEquals');

        $referenceAdapter = $this->prophesize(ReferenceAdapterInterface::class);
        $referenceAdapter->storeReference(Argument::type(ReferenceInterface::class))->shouldBeCalled()->will(
            function ($args) use ($assertEquals, $data, $referenceKey) {
                call_user_func_array($assertEquals, array($referenceKey, $args[0]->getKey()));
                call_user_func_array($assertEquals, array($data, $args[0]->toArray()));
            }
        );
        $commitManager = $this->prophesize(CommitManagerInterface::class);

        $manager = new ReferenceManager($referenceAdapter->reveal(), $commitManager->reveal(), $factory);
        $reference = $manager->create($user->reveal(), $commit->reveal(), $referenceName);

        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($referenceKey, $reference->getKey());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
        $this->assertEquals($data, $reference->toArray());
    }
}
