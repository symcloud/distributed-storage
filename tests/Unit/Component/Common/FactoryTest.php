<?php

namespace Unit\Component\Common;

use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\Common\Factory;
use Symcloud\Component\MetadataStorage\Model\CommitInterface;
use Symcloud\Component\MetadataStorage\Model\TreeInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $algo = 'md5';
    private $secret = 'ThisIsMySecret';
    private $data = 'This is my data';

    public function testCreateBlob()
    {
        $factory = new Factory($this->algo, $this->secret);

        $result = $factory->createBlob($this->data);

        $this->assertEquals(hash_hmac($this->algo, $this->data, $this->secret), $result->getHash());
        $this->assertEquals($this->data, $result->getData());
    }

    public function testCreateBlobWithHash()
    {
        $hash = 'my-hash';

        $factory = new Factory($this->algo, $this->secret);

        $result = $factory->createBlob($this->data, $hash);

        $this->assertEquals($hash, $result->getHash());
        $this->assertEquals($this->data, $result->getData());
    }

    public function testCreateBlobFileSingleBlob()
    {
        $blobHash = 'my-blob-hash';
        $fileHash = 'my-file-hash';

        $blob = new BlobModel();
        $blob->setHash($blobHash);
        $blob->setData($this->data);

        $factory = new Factory($this->algo, $this->secret);
        $result = $factory->createBlobFile($fileHash, array($blob));

        $this->assertEquals($fileHash, $result->getHash());
        $this->assertEquals(array($blob), $result->getBlobs());
        $this->assertEquals($this->data, $result->getContent());
    }

    public function testCreateBlobFileMultipleBlob()
    {
        $blobHashs = array(
            'my-blob-hash-1',
            'my-blob-hash-2'
        );
        $fileHash = 'my-file-hash';

        /** @var BlobModel[] $blobs */
        $blobs = array(new BlobModel(), new BlobModel());
        $blobs[0]->setHash($blobHashs[0]);
        $blobs[0]->setData($this->data);
        $blobs[1]->setHash($blobHashs[1]);
        $blobs[1]->setData(strrev($this->data));

        $factory = new Factory($this->algo, $this->secret);
        $result = $factory->createBlobFile($fileHash, $blobs);

        $this->assertEquals($fileHash, $result->getHash());
        $this->assertEquals($blobs, $result->getBlobs());
        $this->assertEquals($this->data . strrev($this->data), $result->getContent());
    }

    public function testCreateFile()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCreateProxy()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCreateProxyWithoutProxyFactory()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCreateCommit()
    {
        $factory = new Factory($this->algo, $this->secret);

        $message = 'My message';
        $treeHash = 'tree-hash';
        $username = 'johannes';
        $commitHash = 'commit-hash';

        $tree = $this->prophesize(TreeInterface::class);
        $tree->getHash()->willReturn($treeHash);
        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn($username);
        $parentCommit = $this->prophesize(CommitInterface::class);
        $parentCommit->getHash()->willReturn($commitHash);

        $expectedData = array(
            'tree' => $treeHash,
            'message' => $message,
            'parentCommit' => $commitHash,
            'committer' => $username,
            'createdAt' => new \DateTime()
        );
        $expectedHash = $factory->createHash(json_encode($expectedData));

        $result = $factory->createCommit($tree->reveal(), $user->reveal(), $message, $parentCommit->reveal());

        $this->assertEquals($tree->reveal(), $result->getTree());
        $this->assertEquals($user->reveal(), $result->getCommitter());
        $this->assertEquals($parentCommit->reveal(), $result->getParentCommit());
        $this->assertEquals($message, $result->getMessage());
        $this->assertInstanceOf(\DateTime::class, $result->getCreatedAt());
        $this->assertEquals($expectedData, $result->toArray());
        $this->assertEquals($expectedHash, $result->getHash());
    }

    public function testCreateHash()
    {
        $factory = new Factory($this->algo, $this->secret);

        $hash = $factory->createHash($this->data);
        $this->assertEquals(hash_hmac($this->algo, $this->data, $this->secret), $hash);
    }

    public function testCreateFileHash()
    {
        $fileName = tempnam('', 'test-file');
        file_put_contents($fileName, $this->data);

        $factory = new Factory($this->algo, $this->secret);

        $hash = $factory->createFileHash($fileName);
        $this->assertEquals(hash_hmac($this->algo, $this->data, $this->secret), $hash);
    }
}
