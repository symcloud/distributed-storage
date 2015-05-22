<?php

namespace Integration\Component\Commit;

use Integration\Parts\CommitManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Policy;
use Symcloud\Component\Database\Model\Tree\Tree;

class CommitManagerTest extends ProphecyTestCase
{
    use CommitManagerTrait;

    public function testStoreCommit()
    {
        $commitManager = $this->getCommitManager();
        $database = $this->getDatabase();

        $username = 'johannes';
        $message = 'My message';

        $user = $this->getUserProvider()->loadUserByUsername($username);
        $tree = new Tree();
        $tree->setPolicy(new Policy());
        $tree->setName('');
        $tree->setPath('/');
        $database->store($tree);

        $commit = $commitManager->commit($tree, $user, $message);
        $this->assertTrue($database->contains($commit->getHash()));

        $this->assertEquals($tree->getHash(), $commit->getTree()->getHash());
        $this->assertEquals($user->getUsername(), $commit->getCommitter()->getUsername());
        $this->assertEquals($message, $commit->getMessage());
        $this->assertInstanceOf(\DateTime::class, $commit->getCreatedAt());
        $this->assertEquals(null, $commit->getParentCommit());

        /** @var CommitInterface $result */
        $result = $database->fetch($commit->getHash());

        $this->assertEquals($tree->getHash(), $result->getTree()->getHash());
        $this->assertEquals($user->getUsername(), $result->getCommitter()->getUsername());
        $this->assertEquals($message, $result->getMessage());
        $this->assertInstanceOf(\DateTime::class, $result->getCreatedAt());
        $this->assertEquals(null, $result->getParentCommit());
    }

    public function testStoreCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testFetchCommit()
    {
        $commitManager = $this->getCommitManager();
        $database = $this->getDatabase();

        $username = 'johannes';
        $message = 'My message';

        $user = $this->getUserProvider()->loadUserByUsername($username);
        $tree = new Tree();
        $tree->setPolicy(new Policy());
        $tree->setName('');
        $tree->setPath('/');
        $database->store($tree);

        $commit = new Commit();
        $commit->setPolicy(new Policy());
        $commit->setMessage($message);
        $commit->setCommitter($user);
        $commit->setCreatedAt(new \DateTime());
        $commit->setTree($tree);
        $commit->setParentCommit(null);
        $database->store($commit);

        $commitManager->fetch($commit->getHash());

        $this->assertEquals($tree->getHash(), $commit->getTree()->getHash());
        $this->assertEquals($user->getUsername(), $commit->getCommitter()->getUsername());
        $this->assertEquals($message, $commit->getMessage());
        $this->assertInstanceOf(\DateTime::class, $commit->getCreatedAt());
        $this->assertEquals(null, $commit->getParentCommit());
    }

    public function testFetchCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now, missing tree-manager');
    }

    public function testFetchCommitProxy()
    {
        $this->markTestIncomplete('This test is not implemented until now, missing tree-manager');
    }
}
