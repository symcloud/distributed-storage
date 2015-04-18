<?php

namespace Symcloud\Component\BlobStorage\Exception;

class BlobAlreadyExistsException extends \Exception
{
    /**
     * @var string
     */
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
