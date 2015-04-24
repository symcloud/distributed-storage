<?php

namespace Unit\Component\Metadata;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Commit\CommitAdapterInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
                'tree' => $treeHash,
                'message' => $message,
                'parentCommit' => null,
                'committer' => $username,
                'createdAt' => new \DateTime()
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

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commitManager = new CommitManager($factory, $commitAdapter->reveal());
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

    public function testFetch()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testFetchWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }
}
