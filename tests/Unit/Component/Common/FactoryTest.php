<?php

namespace Unit\Component\Common;

use Symcloud\Component\Common\Factory;
use Symfony\Component\Security\Core\User\UserInterface;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $algo = 'md5';
    private $secret = 'ThisIsMySecret';
    private $data = 'This is my data';

    public function testCreateProxy()
    {
        $this->markTestIncomplete('This test is not implemented until now');
    }

    public function testCreateProxyWithoutProxyFactory()
    {
        $this->markTestIncomplete('This test is not implemented until now');
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
