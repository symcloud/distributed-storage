<?php

namespace Integration\Component\Commit;

use Basho\Riak\Bucket;
use Integration\Parts\CommitManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
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

    /**
     * @var mixed
     */
    private $treeManagerMock;

    protected function setUp()
    {
        $this->clearBucket($this->getMetadataBucket());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getCommitManager(), $this->getMetadataBucket())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param CommitManagerInterface $commitManager
     * @param Bucket $metadataBucket
     */
    public function testStoreCommit(CommitManagerInterface $commitManager, Bucket $metadataBucket)
    {
        $treeHash = 'tree-hash';
        $username = 'johannes';
        $message = 'My message';

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);
        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $commitManager->commit($tree->reveal(), $user->reveal(), $message);

        $response = $this->fetchBucketKeys($metadataBucket);
        $this->assertContains($commit->getHash(), $response->getObject()->getData()->keys);

        $response = $this->fetchObject($commit->getHash(), $metadataBucket);
        $this->assertEquals($commit->toArray(), json_decode($response->getObject()->getData(), true));

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
