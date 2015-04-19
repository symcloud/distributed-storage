<?php

namespace Unit\Component\Common;

use Symcloud\Component\Common\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateBlob()
    {
        $algo = 'md5';
        $secret = 'ThisIsMySecret';
        $data = 'This is my data';

        $factory = new Factory($algo, $secret);

        $result = $factory->createBlob($data);

        $this->assertEquals(hash_hmac($algo, $data, $secret), $result->getHash());
        $this->assertEquals($data, $result->getData());
    }

    public function testCreateBlobWithHash()
    {
        $algo = 'md5';
        $secret = 'ThisIsMySecret';
        $data = 'This is my data';
        $hash = 'my-hash';

        $factory = new Factory($algo, $secret);

        $result = $factory->createBlob($data, $hash);

        $this->assertEquals($hash, $result->getHash());
        $this->assertEquals($data, $result->getData());
    }

    public function testCreateFile()
    {
        // TODO create file test
    }

    public function testCreateHash()
    {
        // TODO create hash test
    }

    public function testCreateFileHash()
    {
        // TODO create file hash test
    }
}
