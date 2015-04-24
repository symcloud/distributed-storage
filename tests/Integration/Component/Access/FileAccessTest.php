<?php

namespace Integration\Component\Access;

use Basho\Riak\Bucket;
use Integration\Parts\BlobFileManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Access\FileManager;
use Symcloud\Component\Access\FileManagerInterface;
use Symcloud\Component\MetadataStorage\MetadataManager;
use Symcloud\Component\MetadataStorage\MetadataManagerInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManager;
use Symcloud\Component\MetadataStorage\Reference\ReferenceManagerInterface;
use Symcloud\Component\MetadataStorage\Tree\TreeManager;
use Symcloud\Component\MetadataStorage\Tree\TreeManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileAccessTest extends ProphecyTestCase
{
    use BlobFileManagerTrait;

    /**
     * @var FileManagerInterface
     */
    private $fileManager;

    /**
     * @var ReferenceManagerInterface
     */
    private $referenceManager;

    /**
     * @var TreeManagerInterface
     */
    private $treeManager;

    /**
     * @var MetadataManagerInterface
     */
    private $metadataManager;

    /**
     * @var Bucket
     */
    private $metadataBucket;

    protected function setUp()
    {
        $riak = $this->getRiak();
        $blobBucket = $this->getBlobBucket();
        $blobFileBucket = $this->getBlobFileBucket();
        $metadataBucket = $this->getMetadataBucket();

        $this->clearBucket($blobBucket, $riak);
        $this->clearBucket($blobFileBucket, $riak);
        $this->clearBucket($blobFileBucket, $riak);
        $this->clearBucket($metadataBucket, $riak);

        parent::setUp();
    }

    public function managerProvider()
    {
        return array(array($this->getFileManager()));
    }

    /**
     * @dataProvider managerProvider
     *
     * @param FileManagerInterface $fileManager
     * @param UserInterface $user
     * @param string $path
     * @param string $depth
     * @param string $title
     * @param string $description
     * @param string $fileHash
     * @param string $content
     */
    public function testGetByPath(
        FileManagerInterface $fileManager,
        UserInterface $user,
        $path,
        $depth,
        $title,
        $description,
        $fileHash,
        $content
    ) {
        $result = $fileManager->getByPath($path, $user);

        $this->assertEquals($depth, $result->getDepth());
        $this->assertEquals($path, $result->getPath());
        $this->assertEquals($title, $result->getTitle());
        $this->assertEquals($description, $result->getDescription());
        $this->assertEquals($fileHash, $result->getFileHash());
        $this->assertEquals($content, $result->getContent());
        $this->assertInstanceOf(KeyValueInterface::class, $result->getMetadataStore());
    }

    protected function getFileManager()
    {
        if (!$this->fileManager) {
            $this->fileManager = new FileManager(
                $this->getReferenceManager(),
                $this->getTreeManager(),
                $this->getBlobFileManager(),
                $this->getMetadataManager(),
                $this->getFactory()
            );
        }

        return $this->fileManager;
    }

    protected function getReferenceManager()
    {
        if (!$this->referenceManager) {
            $this->referenceManager = new ReferenceManager();
        }

        return $this->referenceManager;
    }

    protected function getTreeManager()
    {
        if (!$this->treeManager) {
            $this->treeManager = new TreeManager();
        }

        return $this->treeManager;
    }

    protected function getMetadataManager()
    {
        if (!$this->metadataManager) {
            $this->metadataManager = new MetadataManager();
        }

        return $this->metadataManager;
    }

    private function getMetadataBucket()
    {
        if (!$this->metadataBucket) {
            $this->metadataBucket = new Bucket('test-metadata');
        }

        return $this->metadataBucket;
    }
}
