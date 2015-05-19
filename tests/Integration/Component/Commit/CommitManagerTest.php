<?php

namespace Integration\Component\Commit;

use Integration\Parts\CommitManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\MetadataStorage\Commit\CommitManagerInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class CommitManagerTest extends ProphecyTestCase
{
    use CommitManagerTrait;

    /**
     * @var mixed
     */
    private $userProviderMock;

    protected function setUp()
    {
        $this->clearBucket($this->getMetadataNamespace());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getCommitManager(), $this->getMetadataNamespace())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param CommitManagerInterface $commitManager
     * @param RiakNamespace $metadataNamespace
     */
    public function testStoreCommit(CommitManagerInterface $commitManager, RiakNamespace $metadataNamespace)
    {
        $treeHash = 'tree-hash';
        $username = 'johannes';
        $message = 'My message';

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);
        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $commitManager->commit($tree->reveal(), $user->reveal(), $message);

        $keys = $this->fetchBucketKeys($metadataNamespace);
        $this->assertContains($commit->getHash(), $keys);

        $response = $this->fetchObject($commit->getHash(), $metadataNamespace);
        $json = $response->getValue()->getValue()->getContents();
        $this->assertEquals($commit->toArray(), json_decode($json, true));

        $this->assertEquals($tree->reveal(), $commit->getTree());
        $this->assertEquals($user->reveal(), $commit->getCommitter());
        $this->assertEquals($message, $commit->getMessage());
        $this->assertInstanceOf(\DateTime::class, $commit->getCreatedAt());
        $this->assertEquals(null, $commit->getParentCommit());
    }

    public function testStoreCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testFetchCommit()
    {
        $this->markTestIncomplete('This test is not implemented until now, missing tree-manager');
    }

    public function testFetchCommitWithParent()
    {
        $this->markTestIncomplete('This test is not implemented until now, missing tree-manager');
    }

    public function  testFetchCommitProxy()
    {
        $this->markTestIncomplete('This test is not implemented until now, missing tree-manager');
    }

    public function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
