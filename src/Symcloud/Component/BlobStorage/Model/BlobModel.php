<?php

namespace Symcloud\Component\BlobStorage\Model;

class BlobModel implements BlobInterface
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $data;

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
