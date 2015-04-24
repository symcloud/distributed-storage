<?php

namespace Integration\Parts;

trait TestFileTrait
{
    protected function generateTestFile($length)
    {
        $data = $this->generateString($length);
        $fileName = tempnam('', 'test-file');
        file_put_contents($fileName, $data);

        return array($data, $fileName);
    }

    protected function generateString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $length; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randstring;
    }
}
