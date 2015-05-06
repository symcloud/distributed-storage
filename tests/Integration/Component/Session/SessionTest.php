<?php

namespace Integration\Component\Session;

use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SessionTest extends ProphecyTestCase
{
    use ReferenceManagerTrait;

    /**
     * @var mixed
     */
    private $userProviderMock;

    protected function setUp()
    {
        parent::setUp();

        $this->clearBucket($this->getMetadataBucket());
        $this->clearBucket($this->getBlobFileBucket());
        $this->clearBucket($this->getBlobBucket());
    }

    public function testInit()
    {
        $username = 'johannes';
        $referenceName = 'HEAD';

        $userMock = $this->prophesize(UserInterface::class);
        $userMock->getUsername()->willReturn($username);
        $user = $userMock->reveal();

        $session = new Session(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager(),
            $referenceName,
            $user
        );

        $session->init();
        $root = $session->getRoot();

        $this->assertInstanceOf(TreeInterface::class, $root);
        $this->assertEquals('/', $root->getPath());
        $this->assertEquals(array(), $root->getChildren());

        $keys = $this->fetchBucketKeys($this->getMetadataBucket())->getObject()->getData()->keys;

        $objects = array();
        foreach ($keys as $key) {
            $response = $this->fetchObject($key, $this->getMetadataBucket());
            if ($response->isSuccess()) {
                $objects[$key] = $response->getObject()->getData();
            }
        }

        $referenceKey = $username . '-' . $referenceName;
        $this->assertArrayHasKey($referenceKey, $objects);

        $reference = (array)$objects[$referenceKey];
        $commitKey = $reference[ReferenceInterface::COMMIT_KEY];
        $this->assertArrayHasKey($commitKey, $objects);

        $commit = (array)$objects[$commitKey];
        $treeKey = $commit[CommitInterface::TREE_KEY];
        $this->assertArrayHasKey($treeKey, $objects);

        $tree = (array)$objects[$treeKey];
        $this->assertArrayHasKey(TreeInterface::TYPE_KEY, $tree);
        $this->assertArrayHasKey(TreeInterface::CHILDREN_KEY, $tree);
        $this->assertArrayHasKey(TreeInterface::PATH_KEY, $tree);
        $this->assertArrayHasKey(TreeInterface::ROOT_KEY, $tree);

        $this->assertEquals(TreeInterface::TREE_TYPE, $tree[TreeInterface::TYPE_KEY]);
        $this->assertEquals('/', $tree[TreeInterface::PATH_KEY]);
        $this->assertNull($tree[TreeInterface::ROOT_KEY]);
        $this->assertEquals(
            array(TreeInterface::TREE_TYPE => array(), TreeInterface::FILE_TYPE => array()),
            (array)$tree[TreeInterface::CHILDREN_KEY]
        );
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
