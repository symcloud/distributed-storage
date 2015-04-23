<?php

namespace Symcloud\Component\Access\Exception;

class NotAFileException extends \Exception
{
    /**
     * @var string
     */
    private $path;

    /**
     * NotAFileException constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
