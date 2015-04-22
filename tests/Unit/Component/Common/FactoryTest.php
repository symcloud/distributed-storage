<?php

namespace Unit\Component\Common;

use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\Common\Factory;

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

    public function testCreateFileSingleBlob()
    {
        $blobHash = 'my-blob-hash';
        $fileHash = 'my-file-hash';

        $blob = new BlobModel();
        $blob->setHash($blobHash);
        $blob->setData($this->data);

        $factory = new Factory($this->algo, $this->secret);
        $result = $factory->createFile($fileHash, array($blob));

        $this->assertEquals($fileHash, $result->getHash());
        $this->assertEquals(array($blob), $result->getBlobs());
        $this->assertEquals($this->data, $result->getContent());
    }

    public function testCreateFileMultipleBlob()
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
        $result = $factory->createFile($fileHash, $blobs);

        $this->assertEquals($fileHash, $result->getHash());
        $this->assertEquals($blobs, $result->getBlobs());
        $this->assertEquals($this->data . strrev($this->data), $result->getContent());
    }

    public function testCreateHash()
    {
        $factory = new Factory($this->algo, $this->secret);

        $hash = $factory->createHash($this->data);
        $this->assertEquals(hash_hmac($this->algo, $this->data, $this->secret), $hash);
    }

    public function testCreateFileHash()
    {
        // TODO create file hash test
        $fileName = tempnam('', 'test-file');
        file_put_contents($fileName, $this->data);

        $factory = new Factory($this->algo, $this->secret);

        $hash = $factory->createFileHash($fileName);
        $this->assertEquals(hash_hmac($this->algo, $this->data, $this->secret), $hash);
    }
}
