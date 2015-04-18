<?php

namespace Symcloud\Component\BlobStorage\Exception;

class BlobNotFoundException extends \Exception
{
    private $hash;

    /**
     * BlobNotFoundException constructor.
     * @param $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }
}
