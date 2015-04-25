<?php

namespace Integration\Riak;

use Basho\Riak\Bucket;
use Integration\Parts\SerializeAdapterTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Riak\RiakSerializeAdapter;
use Symfony\Component\Security\Core\User\UserInterface;

class RiakSerializeAdapterTest extends ProphecyTestCase
{
    use SerializeAdapterTrait;

    protected function setUp()
    {
        $this->clearBucket($this->getMetadataBucket());

        parent::setUp();
    }

    public function adapterProvider()
    {
        return array(
            array($this->getSerializeAdapter(), $this->getMetadataBucket(), $this->getFactory())
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakSerializeAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testStoreCommit(
        RiakSerializeAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $treeHash = 'tree-hash';
        $username = 'johannes';
        $createdAt = new \DateTime();
        $message = 'My message';

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $commit = $factory->createCommit($tree->reveal(), $user->reveal(), $createdAt, $message);
        $this->assertTrue($adapter->storeCommit($commit));

        $response = $this->fetchObject($commit->getHash(), $metadataBucket);
        $this->assertEquals(
            json_encode(
                array(
                    CommitInterface::TREE_KEY => $treeHash,
                    CommitInterface::MESSAGE_KEY => $message,
                    CommitInterface::PARENT_COMMIT_KEY => null,
                    CommitInterface::COMMITTER_KEY => $username,
                    CommitInterface::CREATED_AT_KEY => $createdAt->format(\DateTime::ISO8601)
                )
            ),
            $response->getObject()->getData()
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakSerializeAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testStoreCommitWithParent(
        RiakSerializeAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakSerializeAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testFetchCommit(
        RiakSerializeAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $treeHash = 'tree-hash';
        $username = 'johannes';
        $createdAt = new \DateTime();
        $message = 'My message';

        $data = array(
            CommitInterface::TREE_KEY => $treeHash,
            CommitInterface::MESSAGE_KEY => $message,
            CommitInterface::PARENT_COMMIT_KEY => null,
            CommitInterface::COMMITTER_KEY => $username,
            CommitInterface::CREATED_AT_KEY => $createdAt->format(\DateTime::ISO8601)
        );
        $commitHash = $factory->createHash(json_encode($data));

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $this->storeObject($commitHash, $data, $metadataBucket);

        $result = $adapter->fetchCommit($commitHash);
        $this->assertEquals($data, $result);

        $response = $this->fetchBucketKeys($metadataBucket);
        $this->assertContains($commitHash, $response->getObject()->getData()->keys);

        $response = $this->fetchObject($commitHash, $metadataBucket);
        $this->assertEquals($data, (array) $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakSerializeAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testFetchCommitWithParent(
        RiakSerializeAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $this->markTestIncomplete('This test is not implemented until now');
    }
}
