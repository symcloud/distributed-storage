<?php

namespace Unit\Component\FileStorage;

use Symcloud\Component\FileStorage\FileSplitter;

class FileSplitterTest extends \PHPUnit_Framework_TestCase
{
    public function adapterProvider()
    {
        return array(
            array(($data = $this->generateString(5)), 10, array($data)),
            array(($data = $this->generateString(20)), 10, array(substr($data, 0, 10), substr($data, 10, 10))),
            array(
                ($data = $this->generateString(200)),
                50,
                array(
                    substr($data, 0, 50),
                    substr($data, 50, 50),
                    substr($data, 100, 50),
                    substr($data, 150, 50)
                )
            )
        );
    }

    /**
     * @dataProvider adapterProvider
     * @param string $data
     * @param int $chunkLength
     * @param string $result
     */
    public function testSplit($data, $chunkLength, $result)
    {
        $chunks = array();
        $fileName = tempnam('', 'splitter-test-file');
        file_put_contents($fileName, $data);

        $splitter = new FileSplitter($chunkLength);

        $splitter->split(
            $fileName,
            function ($index, $chunk) use (&$chunks) {
                $chunks[$index] = $chunk;
            }
        );

        $this->assertEquals(
            $result,
            $chunks
        );
    }

    private function generateString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randstring;
    }
}
