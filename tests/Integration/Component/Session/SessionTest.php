<?php

namespace Integration\Component\Session;

use Basho\Riak\Bucket;
use Integration\Parts\ReferenceManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SessionTest extends ProphecyTestCase
{
    use ReferenceManagerTrait, TestFileTrait;

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

        $objects =$this->getObjects($this->getMetadataBucket());

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

    public function testUpload()
    {
        /**
         * setup
         */
        $username = 'johannes';
        $referenceName = 'HEAD';

        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

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

        /**
         * do it
         */
        $blobFile = $session->upload($fileName);
        $this->assertEquals($fileHash, $blobFile->getHash());
        $this->assertEquals($this->getFactory()->createHash($fileContent), $blobFile->getHash());
        $this->assertEquals($fileContent, $blobFile->getContent());

        /**
         * asserts on database
         */
        $blob1 = substr($fileContent, 0, 100);
        $hash1 = $this->getFactory()->createHash($blob1);
        $blob2 = substr($fileContent, 100, 100);
        $hash2 = $this->getFactory()->createHash($blob2);

        $blobFile = $this->fetchObject($fileHash, $this->getBlobFileBucket())->getObject()->getData();
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobBucket());
        $object2 = $this->fetchObject($hash2, $this->getBlobBucket());

        $this->assertEquals($blob1, $object1->getObject()->getData());
        $this->assertEquals($blob2, $object2->getObject()->getData());
    }

    public function testCreateOrUpdateFile()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCommit()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testDownload()
    {
        $this->markTestIncomplete('This test is not implemented until now');

        /**
         * setup
         */
        $username = 'johannes';
        $referenceName = 'HEAD';

        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

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
        $blobFile = $session->upload($fileName);
        $session->createOrUpdateFile('/test.txt', $blobFile->getHash());
        $session->commit('added test.txt');

        /**
         * do it
         */
        $result = $session->download('/test.txt');
        $this->assertEquals($fileHash, $result->getHash());
        $this->assertEquals($fileHash, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent, $result->getContent());

        /**
         * asserts on database
         */
        $blob1 = substr($fileContent, 0, 100);
        $hash1 = $this->getFactory()->createHash($blob1);
        $blob2 = substr($fileContent, 100, 100);
        $hash2 = $this->getFactory()->createHash($blob2);

        $blobFile = $this->fetchObject($fileHash, $this->getBlobFileBucket())->getObject()->getData();
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobBucket());
        $object2 = $this->fetchObject($hash2, $this->getBlobBucket());

        $this->assertEquals($blob1, $object1->getObject()->getData());
        $this->assertEquals($blob2, $object2->getObject()->getData());
    }

    private function getObjects(Bucket $bucket)
    {
        $keys = $this->fetchBucketKeys($bucket)->getObject()->getData()->keys;

        $objects = array();
        foreach ($keys as $key) {
            $response = $this->fetchObject($key, $this->getMetadataBucket());
            if ($response->isSuccess()) {
                $objects[$key] = $response->getObject()->getData();
            }
        }

        return $objects;
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        return $this->userProviderMock->reveal();
    }
}
