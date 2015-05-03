<?php

namespace Symcloud\Component\MetadataStorage\Exception;

class NotATreeException extends \Exception
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var string
     */
    private $path;

    /**
     * NotATreeException constructor.
     * @param string $hash
     * @param string $path
     */
    public function __construct($hash, $path)
    {
        parent::__construct(sprintf('Node with path "%s" is not a tree', $path));

        $this->hash = $hash;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
