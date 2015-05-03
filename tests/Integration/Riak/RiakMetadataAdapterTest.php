<?php

namespace Integration\Riak;

use Basho\Riak\Bucket;
use Integration\Parts\FactoryTrait;
use Integration\Parts\MetadataAdapterTrait;
use Integration\Parts\RiakTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Common\FactoryInterface;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Riak\RiakMetadataAdapter;
use Symfony\Component\Security\Core\User\UserInterface;

class RiakMetadataAdapterTest extends ProphecyTestCase
{
    use MetadataAdapterTrait, RiakTrait, FactoryTrait;

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
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testStoreCommit(
        RiakMetadataAdapter $adapter,
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
                array(
                    CommitInterface::TREE_KEY => $treeHash,
                    CommitInterface::MESSAGE_KEY => $message,
                    CommitInterface::PARENT_COMMIT_KEY => null,
                    CommitInterface::COMMITTER_KEY => $username,
                    CommitInterface::CREATED_AT_KEY => $createdAt->format(\DateTime::ISO8601)
            ),
            (array)$response->getObject()->getData()
        );
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testStoreCommitWithParent(
        RiakMetadataAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testFetchCommit(
        RiakMetadataAdapter $adapter,
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

        $result = $adapter->fetchCommitData($commitHash);
        $this->assertEquals($data, $result);

        $response = $this->fetchBucketKeys($metadataBucket);
        $this->assertContains($commitHash, $response->getObject()->getData()->keys);

        $response = $this->fetchObject($commitHash, $metadataBucket);
        $this->assertEquals($data, (array) $response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testFetchCommitWithParent(
        RiakMetadataAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testStoreReference(
        RiakMetadataAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $username = 'johannes';
        $referenceName = 'HEAD';
        $referenceKey = sprintf('%s-%s', $username, $referenceName);
        $commitHash = 'commit-hash';
        $data = array(
            ReferenceInterface::NAME_KEY => $referenceName,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::USER_KEY => $username,
        );

        $commit = $this->prophesize(CommitInterface::class);
        $commit->getHash()->willReturn($commitHash);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $reference = $factory->createReference($commit->reveal(), $user->reveal(), $referenceName);

        $this->assertTrue($adapter->storeReference($reference));

        $this->assertEquals($referenceKey, $reference->getKey());
        $this->assertEquals($referenceName, $reference->getName());
        $this->assertEquals($user->reveal(), $reference->getUser());
        $this->assertEquals($commit->reveal(), $reference->getCommit());
        $this->assertEquals($data, $reference->toArray());

        $response = $this->fetchBucketKeys($metadataBucket);
        $this->assertContains($referenceKey, $response->getObject()->getData()->keys);

        $response = $this->fetchObject($referenceKey, $metadataBucket);
        $this->assertEquals($data, (array)$response->getObject()->getData());
    }

    /**
     * @dataProvider adapterProvider
     *
     * @param RiakMetadataAdapter $adapter
     * @param Bucket $metadataBucket
     * @param FactoryInterface $factory
     */
    public function testFetchReference(
        RiakMetadataAdapter $adapter,
        Bucket $metadataBucket,
        FactoryInterface $factory
    ) {
        $username = 'johannes';
        $referenceName = 'HEAD';
        $referenceKey = sprintf('%s-%s', $username, $referenceName);
        $commitHash = 'commit-hash';
        $data = array(
            ReferenceInterface::NAME_KEY => $referenceName,
            ReferenceInterface::COMMIT_KEY => $commitHash,
            ReferenceInterface::USER_KEY => $username,
        );

        $this->storeObject($referenceKey, $data, $metadataBucket);

        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);

        $reference = $adapter->fetchReferenceData($user->reveal(), $referenceName);

        $this->assertEquals($data, $reference);
    }
}
