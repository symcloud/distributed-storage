<?php

namespace Integration\Component\Session;

use Integration\Parts\ReferenceManagerTrait;
use Integration\Parts\TestFileTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\Database\Model\Tree\TreeInterface;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Search\ZendLuceneAdapter;
use Symcloud\Component\Session\Session;
use Symcloud\Component\Session\SessionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\User\UserInterface;

class SessionTest extends ProphecyTestCase
{
    use ReferenceManagerTrait, TestFileTrait;

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

    /**
     * @var SearchAdapterInterface
     */
    private $adapter;

    protected function setUp()
    {
        parent::setUp();

        $this->user = $this->getUserProvider()->loadUserByUsername($this->username);

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
        $database = $this->getDatabase();
        /** @var ReferenceInterface $reference */
        $reference = $database->fetch($this->referenceName);
        $commit = $reference->getCommit();
        $tree = $commit->getTree();
        $this->assertEquals(array(), $tree->getChildren());
        $this->assertEquals('/', $tree->getPath());
        $this->assertEquals('', $tree->getName());
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
        $blobFile = $this->session->upload($fileName, 'application/json', 999);
        $this->assertNotNull($blobFile->getHash());
        $this->assertEquals($fileHash, $blobFile->getFileHash());
        $this->assertEquals($this->getFactory()->createHash($fileContent), $blobFile->getFileHash());
        $this->assertEquals($fileContent, $blobFile->getContent());
        $this->assertEquals('application/json', $blobFile->getMimeType());
        $this->assertEquals(999, $blobFile->getSize());

        /**
         * asserts on database
         */
        $database = $this->getDatabase();
        $blob1 = substr($fileContent, 0, 100);
        $hash1 = $this->getFactory()->createHash($blob1);
        $blob2 = substr($fileContent, 100, 100);
        $hash2 = $this->getFactory()->createHash($blob2);

        $this->assertEquals($blob1, $database->fetch($hash1)->getData());
        $this->assertEquals($blob2, $database->fetch($hash2)->getData());
        $this->assertEquals($hash1, $database->fetch($hash1)->getHash());
        $this->assertEquals($hash2, $database->fetch($hash2)->getHash());
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
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName, $mimeType, $size);
        $this->session->createOrUpdateFile('/test.txt', $blobFile->getFileHash());
        $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test.txt');
        $this->assertEquals($blobFile->getHash(), $result->getHash());
        $this->assertEquals($fileHash, $result->getFileHash());
        $this->assertEquals($fileHash, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent, $result->getContent());
        $this->assertEquals($mimeType, $result->getMimeType());
        $this->assertEquals($size, $result->getSize());
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
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1, $mimeType, $size);
        $this->session->createOrUpdateFile('/test1.txt', $blobFile1->getFileHash());
        $this->session->commit('added test1.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test1.txt');
        $this->assertEquals($blobFile1->getHash(), $result->getHash());
        $this->assertEquals($fileHash1, $result->getFileHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        /**
         * setup second test
         */
        $blobFile2 = $this->session->upload($fileName2, $mimeType, $size);
        $this->session->createOrUpdateFile('/test2.txt', $blobFile2->getFileHash());
        $this->session->commit('added test2.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test1.txt');
        $this->assertEquals($blobFile1->getHash(), $result->getHash());
        $this->assertEquals($fileHash1, $result->getFileHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        $result = $this->session->download('/test2.txt');
        $this->assertEquals($blobFile2->getHash(), $result->getHash());
        $this->assertEquals($fileHash2, $result->getFileHash());
        $this->assertEquals($fileHash2, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent2, $result->getContent());
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
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1, $mimeType, $size);
        $this->session->createOrUpdateFile('/test.txt', $blobFile1->getFileHash());
        $commit1 = $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test.txt');
        $this->assertEquals($fileHash1, $result->getFileHash());
        $this->assertEquals($fileHash1, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent1, $result->getContent());

        /**
         * setup second part
         */
        $blobFile2 = $this->session->upload($fileName2, $mimeType, $size);
        $this->session->createOrUpdateFile('/test.txt', $blobFile2->getFileHash());
        $commit2 = $this->session->commit('updated test.txt');

        /**
         * do it second part
         */
        $result = $this->session->download('/test.txt');
        $this->assertEquals($fileHash2, $result->getFileHash());
        $this->assertEquals($fileHash2, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent2, $result->getContent());

        /**
         * download by commit
         */
        $result1 = $this->session->download('/test.txt', $commit1);
        $result2 = $this->session->download('/test.txt', $commit2);

        $this->assertEquals($blobFile1->getHash(), $result1->getHash());
        $this->assertEquals($fileHash1, $result1->getFileHash());
        $this->assertEquals($fileContent1, $result1->getContent());

        $this->assertEquals($blobFile2->getHash(), $result2->getHash());
        $this->assertEquals($fileHash2, $result2->getFileHash());
        $this->assertEquals($fileContent2, $result2->getContent());
    }

    /**
     * 1. Upload /test/test.txt
     * 2. Commit
     * 3. Download /test/test.txt
     */
    public function testDownloadFromTree()
    {
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName, $mimeType, $size);
        $this->session->createOrUpdateFile('/test/test.txt', $blobFile->getFileHash());
        $this->session->commit('added test.txt');

        /**
         * do it
         */
        $result = $this->session->download('/test/test.txt');
        $this->assertEquals($blobFile->getHash(), $result->getHash());
        $this->assertEquals($fileHash, $result->getFileHash());
        $this->assertEquals($fileHash, $this->getFactory()->createHash($result->getContent()));
        $this->assertEquals($fileContent, $result->getContent());
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
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent, $fileName) = $this->generateTestFile(200);
        $fileHash = $this->getFactory()->createFileHash($fileName);

        $this->session->init();
        $blobFile = $this->session->upload($fileName, $mimeType, $size);
        $this->session->createOrUpdateFile('/test.txt', $blobFile->getFileHash());
        $commit1 = $this->session->commit('added test.txt');

        /**
         * do it
         */
        $this->session->deleteFile('/test.txt');
        $this->session->commit('removed test.txt');
        $result = $this->session->download('/test.txt', $commit1);
        $this->assertEquals($result->getFileHash(), $fileHash);
        $this->session->download('/test.txt');
    }

    /**
     * 1. Upload /test/test1.txt
     * 2. Upload /test/test2.txt
     * 3. Commit
     * 4. Get  /test
     */
    public function testGetDirectory()
    {
        $size = 999;
        $mimeType = 'application/json';

        /**
         * setup
         */
        list($fileContent1, $fileName1) = $this->generateTestFile(200);
        $fileHash1 = $this->getFactory()->createFileHash($fileName1);
        list($fileContent2, $fileName2) = $this->generateTestFile(200);
        $fileHash2 = $this->getFactory()->createFileHash($fileName2);

        $this->session->init();
        $blobFile1 = $this->session->upload($fileName1, $mimeType, $size);
        $blobFile2 = $this->session->upload($fileName2, $mimeType, $size);
        $this->session->createOrUpdateFile('/test/test1.txt', $blobFile1->getFileHash());
        $this->session->createOrUpdateFile('/test/test2.txt', $blobFile2->getFileHash());
        $this->session->commit('init test data');

        $directory = $this->session->getDirectory('/test');
        $this->assertEquals($fileHash1, $directory->getChild('test1.txt')->getFileHash());
        $this->assertEquals($fileHash2, $directory->getChild('test2.txt')->getFileHash());
    }

    protected function createSearchAdapter()
    {
        if (!is_dir(__DIR__ . '/lucene')) {
            mkdir(__DIR__ . '/lucene');
        }

        $this->adapter = new ZendLuceneAdapter(__DIR__ . '/lucene', new Filesystem());
        $this->adapter->deindexAll();

        return $this->adapter;
    }
}
