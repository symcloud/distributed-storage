<?php


namespace Symcloud\Component\FileStorage\Exception;


class FileNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $hash;

    /**
     * FileNotFoundException constructor.
     * @param string $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
