<?php

namespace Unit\Component\Metadata;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CommitManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    public function testCommit()
    {
        $treeHash = 'tree-hash';
        $message = 'My message';
        $username = 'johannes';

        $factory = $this->getFactory();
        $expectedData = json_encode(
            array(
                CommitInterface::TREE_KEY => $treeHash,
                CommitInterface::MESSAGE_KEY => $message,
                CommitInterface::PARENT_COMMIT_KEY => null,
                CommitInterface::COMMITTER_KEY => $username,
                CommitInterface::CREATED_AT_KEY => (new \DateTime())->format(\DateTime::ISO8601)
            )
        );
        $expectedHash = $factory->createHash($expectedData);

        $commitAdapter = $this->prophesize(CommitAdapterInterface::class);
        $commitAdapter->storeCommit(Argument::type(CommitInterface::class))
            ->will(
                function ($args) use ($expectedHash, &$calledHash) {
                    $calledHash = $args[0]->getHash();
                }
            );

        $userProvider = $this->prophesize(UserProviderInterface::class);
        $treeManager = $this->prophesize(TreeManagerInterface::class);

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commitManager = new CommitManager(
            $factory,
            $commitAdapter->reveal(),
            $userProvider->reveal(),
            $treeManager->reveal()
        );
        $result = $commitManager->commit($tree->reveal(), $user->reveal(), $message);

        $this->assertEquals($expectedHash, $calledHash);

        $this->assertEquals($expectedHash, $result->getHash());
        $this->assertEquals($message, $result->getMessage());
        $this->assertNull($result->getParentCommit());
        $this->assertEquals($tree->reveal(), $result->getTree());
    }

    public function testCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testFetchCommit()
    {
        $commitHash = 'commit-hash';
        $treeHash = 'tree-hash';
        $message = 'My message';
        $username = 'johannes';
        $createdAt = new \DateTime();

        $factory = $this->getFactory();
        $commitAdapter = $this->prophesize(CommitAdapterInterface::class);
        $commitAdapter->fetchCommit($commitHash)->willReturn(
            array(
                CommitInterface::TREE_KEY => $treeHash,
                CommitInterface::MESSAGE_KEY => $message,
                CommitInterface::PARENT_COMMIT_KEY => null,
                CommitInterface::COMMITTER_KEY => $username,
                CommitInterface::CREATED_AT_KEY => $createdAt->format(\DateTime::ISO8601)
            )
        );

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->getUserByUsername($username)->willReturn($user);
        
        $treeManager = $this->prophesize(TreeManagerInterface::class);
        $treeManager->fetch($treeHash)->willReturn($tree->reveal());

        $commitManager = new CommitManager(
            $factory,
            $commitAdapter->reveal(),
            $userProvider->reveal(),
            $treeManager->reveal()
        );
        $result = $commitManager->fetch($commitHash);

        $this->assertEquals($commitHash, $result->getHash());
        $this->assertEquals($treeHash, $result->getTree()->getHash());
        $this->assertEquals(
            $createdAt->format(\DateTime::ISO8601),
            $result->getCreatedAt()->format(\DateTime::ISO8601)
        );
        $this->assertEquals($message, $result->getMessage());
        $this->assertEquals($username, $result->getCommitter()->getUsername());
        $this->assertEquals(null, $result->getParentCommit());
    }

    public function testFetchCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }
}
