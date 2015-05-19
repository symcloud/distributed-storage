<?php

namespace Integration\Component\Session;

use Integration\Parts\ReferenceManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Riak\Client\Core\Query\RiakNamespace;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\NodeInterface;
use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symcloud\Component\Session\Session;
use Symcloud\Component\Session\SessionInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class SessionTest extends ProphecyTestCase
{
    use ReferenceManagerTrait, TestFileTrait;

    /**
     * @var mixed
     */
    private $userProviderMock;

    /**
     * @var mixed
     */
    private $userMock;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $username = 'johannes';

    /**
     * @var string
     */
    private $referenceName = 'HEAD';

    protected function setUp()
    {
        parent::setUp();

        $this->clearBucket($this->getMetadataNamespace());
        $this->clearBucket($this->getBlobFileNamespace());
        $this->clearBucket($this->getBlobNamespace());

        /**
         * setup
         */
        $this->userMock = $this->prophesize(UserInterface::class);
        $this->userMock->getUsername()->willReturn($this->username);
        $this->user = $this->userMock->reveal();

        $this->session = new Session(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager(),
            $this->referenceName,
            $this->user
        );
    }

    public function testInit()
    {
        /**
         * do it
         */
        $this->session->init();
        $root = $this->session->getRoot();

        $this->assertInstanceOf(TreeInterface::class, $root);
        $this->assertEquals('/', $root->getPath());
        $this->assertEquals(array(), $root->getChildren());

        /**
         * asserts on database
         */
        $objects = $this->getObjects($this->getMetadataNamespace());

        $referenceKey = $this->username . '-' . $this->referenceName;
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
            array(
                TreeInterface::TREE_TYPE => array(),
                TreeInterface::FILE_TYPE => array(),
                TreeInterface::REFERENCE_TYPE => array()
            ),
            (array)$tree[TreeInterface::CHILDREN_KEY]
        );
    }

    public function testUpload()
    {
        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        /**
         * do it
         */
        $blobFile = $this->session->upload($fileName);
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

        $blobFile = json_decode(
            $this->fetchObject($fileHash, $this->getBlobFileNamespace())->getValue()->getValue(),
            true
        );
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobNamespace());
        $object2 = $this->fetchObject($hash2, $this->getBlobNamespace());

        $this->assertEquals($blob1, $object1->getValue()->getValue()->getContents());
        $this->assertEquals($blob2, $object2->getValue()->getValue()->getContents());
    }

    public function testCreateOrUpdateFile()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCommit()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    /**
     * 1. Upload /test.txt
     * 2. Commit
     * 3. Download it
     */
    public function testDownload()
    {
        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName);
        $this->session->createOrUpdateFile('/test.txt', $blobFile->getHash());
        $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test.txt');
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

        $blobFile = json_decode($this->fetchObject($fileHash, $this->getBlobFileNamespace())->getValue()->getValue(),true);
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobNamespace());
        $object2 = $this->fetchObject($hash2, $this->getBlobNamespace());

        $this->assertEquals($blob1, $object1->getValue()->getValue()->getContents());
        $this->assertEquals($blob2, $object2->getValue()->getValue()->getContents());
    }

    /**
     * 1. Upload /test1.txt
     * 2. Commit
     * 3. Download /test1.txt
     * 4. Upload /test2.txt
     * 5. Commit
     * 6. Download /file2.txt
     */
    public function testDownloadMultipleFilesAndCommits()
    {
        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1);
        $this->session->createOrUpdateFile('/test1.txt', $blobFile1->getHash());
        $this->session->commit('added test1.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test1.txt');
        $this->assertEquals($fileHash1, $result->getHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        /**
         * setup second test
         */
        $blobFile2 = $this->session->upload($fileName2);
        $this->session->createOrUpdateFile('/test2.txt', $blobFile2->getHash());
        $this->session->commit('added test2.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test1.txt');
        $this->assertEquals($fileHash1, $result->getHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        $result = $this->session->download('/test2.txt');
        $this->assertEquals($fileHash2, $result->getHash());
        $this->assertEquals($fileHash2, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent2, $result->getContent());

        /**
         * asserts on database
         */
        $blob11 = substr($fileContent1, 0, 100);
        $hash11 = $this->getFactory()->createHash($blob11);
        $blob12 = substr($fileContent1, 100, 100);
        $hash12 = $this->getFactory()->createHash($blob12);

        $blobFile1 = json_decode(
            $this->fetchObject($fileHash1, $this->getBlobFileNamespace())->getValue()->getValue(),
            true
        );
        $this->assertEquals(array($hash11, $hash12), $blobFile1);

        $object11 = $this->fetchObject($hash11, $this->getBlobNamespace());
        $object12 = $this->fetchObject($hash12, $this->getBlobNamespace());

        $this->assertEquals($blob11, $object11->getValue()->getValue()->getContents());
        $this->assertEquals($blob12, $object12->getValue()->getValue()->getContents());

        $blob21 = substr($fileContent2, 0, 100);
        $hash21 = $this->getFactory()->createHash($blob21);
        $blob22 = substr($fileContent2, 100, 100);
        $hash22 = $this->getFactory()->createHash($blob22);

        $blobFile2 = json_decode(
            $this->fetchObject($fileHash2, $this->getBlobFileNamespace())->getValue()->getValue(),
            true
        );
        $this->assertEquals(array($hash21, $hash22), $blobFile2);

        $object21 = $this->fetchObject($hash21, $this->getBlobNamespace());
        $object22 = $this->fetchObject($hash22, $this->getBlobNamespace());

        $this->assertEquals($blob21, $object21->getValue()->getValue()->getContents());
        $this->assertEquals($blob22, $object22->getValue()->getValue()->getContents());
    }

    /**
     * 1. Upload /test.txt
     * 2. Commit
     * 3. Download /test.txt
     * 4. Upload /test.txt
     * 5. Commit
     * 6. Download /test.txt
     */
    public function testDownloadSingleFileAndCommits()
    {
        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1);
        $this->session->createOrUpdateFile('/test.txt', $blobFile1->getHash());
        $commit1 = $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test.txt');
        $this->assertEquals($fileHash1, $result->getHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        /**
         * setup second part
         */
        $blobFile2 = $this->session->upload($fileName2);
        $this->session->createOrUpdateFile('/test.txt', $blobFile2->getHash());
        $commit2 = $this->session->commit('updated test.txt');

        /**
         * do it second part
         */
        $result = $this->session->download('/test.txt');
        $this->assertEquals($fileHash2, $result->getHash());
        $this->assertEquals($fileHash2, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent2, $result->getContent());

        /**
         * download by commit
         */
        $result1 = $this->session->download('/test.txt', $commit1);
        $result2 = $this->session->download('/test.txt', $commit2);

        $this->assertEquals($fileHash1, $result1->getHash());
        $this->assertEquals($fileContent1, $result1->getContent());
        $this->assertEquals($fileHash2, $result2->getHash());
        $this->assertEquals($fileContent2, $result2->getContent());


        /**
         * asserts on database
         */
        $blob1 = substr($fileContent2, 0, 100);
        $hash1 = $this->getFactory()->createHash($blob1);
        $blob2 = substr($fileContent2, 100, 100);
        $hash2 = $this->getFactory()->createHash($blob2);

        $blobFile = json_decode(
            $this->fetchObject($fileHash2, $this->getBlobFileNamespace())->getValue()->getValue(),
            true
        );
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobNamespace());
        $object2 = $this->fetchObject($hash2, $this->getBlobNamespace());

        $this->assertEquals($blob1, $object1->getValue()->getValue()->getContents());
        $this->assertEquals($blob2, $object2->getValue()->getValue()->getContents());
    }

    /**
     * 1. Upload /test/test.txt
     * 2. Commit
     * 3. Download /test/test.txt
     */
    public function testDownloadFromTree()
    {
        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName);
        $this->session->createOrUpdateFile('/test/test.txt', $blobFile->getHash());
        $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test/test.txt');
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

        $blobFile = json_decode($this->fetchObject($fileHash, $this->getBlobFileNamespace())->getValue()->getValue(),true);
        $this->assertEquals(array($hash1, $hash2), $blobFile);

        $object1 = $this->fetchObject($hash1, $this->getBlobNamespace());
        $object2 = $this->fetchObject($hash2, $this->getBlobNamespace());

        $this->assertEquals($blob1, $object1->getValue()->getValue()->getContents());
        $this->assertEquals($blob2, $object2->getValue()->getValue()->getContents());
    }

    /**
     * 1. Upload /test.txt
     * 2. Commit
     * 3. REMOVE
     * 4. COMMIT
     * 5. DOWNLOAD /test.txt from commit-1
     * 6. DOWNLOAD /test.txt
     * @expectedException \Symcloud\Component\Session\Exception\FileNotExistsException
     */
    public function testDelete()
    {
        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName);
        $this->session->createOrUpdateFile('/test.txt', $blobFile->getHash());
        $commit1 = $this->session->commit('added test.txt');

        /**
         * do it
         */
        $this->session->deleteFile('/test.txt');
        $this->session->commit('removed test.txt');
        $result = $this->session->download('/test.txt', $commit1);
        $this->assertEquals($result->getHash(), $fileHash);
        $this->session->download('/test.txt');
    }

    /**
     * 1. Upload /test/test1.txt
     * 2. Upload /test/test1.txt
     * 3. Upload /test.txt
     * 4. Commit
     * 5. Split /test with reference name 'TEST'
     * 6. Create new Session with
     */
    public function testSplit()
    {
        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);
        list($fileContent3, $fileName3) = $this->generateTestFile(200);
        $fileHash3 = $this->getFactory()->createFileHash($fileName3);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1);
        $blobFile2 = $this->session->upload($fileName2);
        $blobFile3 = $this->session->upload($fileName3);
        $this->session->createOrUpdateFile('/test/test1.txt', $blobFile1->getHash());
        $this->session->createOrUpdateFile('/test/test2.txt', $blobFile2->getHash());
        $this->session->createOrUpdateFile('/test.txt', $blobFile3->getHash());
        $commit1 = $this->session->commit('init test data');

        /**
         * do it
         */
        $reference = $this->session->split('/test', 'TEST');
        $this->assertEquals('johannes', $reference->getUser()->getUsername());
        $this->assertEquals('TEST', $reference->getName());
        $this->session->commit('split tree');

        $root = $this->session->getRoot();
        $testFile = $root->getChild('test.txt');
        $this->assertEquals($testFile->getFile()->getHash(), $fileHash3);
        $this->assertEquals($testFile->getType(), NodeInterface::FILE_TYPE);

        $testReference = $root->getChild('test');
        $this->assertEquals($testReference->getType(), NodeInterface::REFERENCE_TYPE);
        $this->assertEquals($testReference->getUser()->getUsername(), $this->username);
        $this->assertEquals($testReference->getReferenceName(), 'TEST');

        $root = $this->session->getRoot($commit1);
        $testFile = $root->getChild('test.txt');
        $this->assertEquals($testFile->getFile()->getHash(), $fileHash3);
        $this->assertEquals($testFile->getType(), NodeInterface::FILE_TYPE);

        $testReference = $root->getChild('test');
        $this->assertEquals($testReference->getType(), NodeInterface::TREE_TYPE);

        $session = new Session(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager(),
            'TEST',
            $this->user
        );

        $file1 = $session->download('/test1.txt');
        $file2 = $session->download('/test2.txt');
        $this->assertEquals($fileHash1, $file1->getHash());
        $this->assertEquals($fileHash2, $file2->getHash());
    }

    /**
     * 1.  Create session for user 2
     * 2.  Upload test1.txt as user 2
     * 3.  Upload test2.txt as user 2
     * 4.  Commit as user 2
     * 5.  Upload test.txt as user 1
     * 6.  Commit as user 1
     * 7.  Mount as user 1 HEAD of user 2 to /test
     * 8.  Commit as user 1
     * 9.  Download /test/test1.txt
     * 10. Download /test/test2.txt
     * 11. Download /test.txt
     */
    public function testMount()
    {
        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);
        list($fileContent3, $fileName3) = $this->generateTestFile(200);
        $fileHash3 = $this->getFactory()->createFileHash($fileName3);

        $user2 = $this->prophesize(UserInterface::class);
        $user2->getUsername()->willReturn('test-user');

        $this->userProviderMock->loadUserByUsername('test-user')->willReturn($user2->reveal());

        $sessionUser2 = new Session(
            $this->getBlobFileManager(),
            $this->getReferenceManager(),
            $this->getTreeManager(),
            $this->getCommitManager(),
            'HEAD',
            $user2->reveal()
        );

        $sessionUser2->init();
        $blobFile1 = $sessionUser2->upload($fileName1);
        $blobFile2 = $sessionUser2->upload($fileName2);
        $sessionUser2->createOrUpdateFile('/test1.txt', $blobFile1->getHash());
        $sessionUser2->createOrUpdateFile('/test2.txt', $blobFile2->getHash());
        $sessionUser2->commit('init test data');

        $this->session->init();
        $blobFile3 = $this->session->upload($fileName3);
        $this->session->createOrUpdateFile('/test.txt', $blobFile3->getHash());
        $this->session->commit('init test data');

        /**
         * do it
         */
        $reference = $this->session->mount('/test', $user2->reveal(), 'HEAD');
        $this->assertEquals('test-user', $reference->getUser()->getUsername());
        $this->assertEquals('HEAD', $reference->getReferenceName());
        $this->assertEquals('test', $reference->getName());
        $this->session->commit('mount tree');

        $file1 = $this->session->download('/test/test1.txt');
        $file2 = $this->session->download('/test/test2.txt');
        $file3 = $this->session->download('/test.txt');

        $this->assertEquals($fileHash1, $file1->getHash());
        $this->assertEquals($fileHash2, $file2->getHash());
        $this->assertEquals($fileHash3, $file3->getHash());
    }

    /**
     * 1. Upload /test/test1.txt
     * 2. Upload /test/test2.txt
     * 3. Commit
     * 4. Get  /test
     */
    public function testGetDirectory()
    {

        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1);
        $blobFile2 = $this->session->upload($fileName2);
        $this->session->createOrUpdateFile('/test/test1.txt', $blobFile1->getHash());
        $this->session->createOrUpdateFile('/test/test2.txt', $blobFile2->getHash());
        $this->session->commit('init test data');

        $directory = $this->session->getDirectory('/test');
        $this->assertEquals($fileHash1, $directory->getChild('test1.txt')->getFileHash());
        $this->assertEquals($fileHash2, $directory->getChild('test2.txt')->getFileHash());
    }

    private function getObjects(RiakNamespace $namespace)
    {
        $keys = $this->fetchBucketKeys($namespace);

        $objects = array();
        foreach ($keys as $key) {
            try {
                $response = $this->fetchObject($key, $this->getMetadataNamespace());
                if (!$response->getNotFound()) {
                    $objects[$key] = json_decode($response->getValue()->getValue()->getContents(), true);
                }
            } catch (\Exception $ex) {
            }
        }

        return $objects;
    }

    protected function createUserProvider()
    {
        $this->userProviderMock = $this->prophesize(UserProviderInterface::class);

        $this->userProviderMock->loadUserByUsername($this->username)->willReturn($this->user);

        return $this->userProviderMock->reveal();
    }
}
