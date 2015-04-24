<?php

namespace Integration\Component\Access;

use Basho\Riak\Bucket;
use Integration\BaseIntegrationTest;
use Symcloud\Component\Access\FileManagerInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FileAccessTest extends BaseIntegrationTest
{
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
        return array(array());
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
}
