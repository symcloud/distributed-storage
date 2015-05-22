<?php

namespace Unit\Component\Metadata;

use Integration\Parts\FactoryTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\DatabaseInterface;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symcloud\Component\MetadataStorage\Commit\CommitManager;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Zend\Stdlib\DateTime;

class CommitManagerTest extends ProphecyTestCase
{
    use FactoryTrait;

    public function testCommit()
    {
        $treeHash = 'tree-hash';
        $message = 'My message';
        $username = 'johannes';

        $factory = $this->getFactory();
        $database = $this->prophesize(DatabaseInterface::class);
        $database->store(Argument::type(CommitInterface::class))
            ->will(
                function ($args) use (&$calledHash) {
                    $args[0]->setHash('my-hash');

                    return $args[0];
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
            $database->reveal(),
            $userProvider->reveal(),
            $treeManager->reveal()
        );
        $result = $commitManager->commit($tree->reveal(), $user->reveal(), $message);

        $this->assertNotNull($result);
        $this->assertNotNull($result->getHash());
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

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = new Commit();
        $commit->setPolicy(new Policy());
        $commit->setHash($commitHash);
        $commit->setTree($tree->reveal());
        $commit->setCommitter($user->reveal());
        $commit->setCreatedAt(new \DateTime);
        $commit->setMessage($message);
        $commit->setParentCommit(null);

        $database = $this->prophesize(DatabaseInterface::class);
        $database->fetch($commitHash, Commit::class)->willReturn($commit);

        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->loadUserByUsername($username)->willReturn($user);

        $treeManager = $this->prophesize(TreeManagerInterface::class);
        $treeManager->fetchProxy($treeHash)->willReturn($tree->reveal());

        $commitManager = new CommitManager(
            $factory,
            $database->reveal(),
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
