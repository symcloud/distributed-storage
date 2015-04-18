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
     * {@inheritdoc}
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
     * {@inheritdoc}
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
