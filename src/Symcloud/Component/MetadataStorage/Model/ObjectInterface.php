<?php
namespace Symcloud\Component\MetadataStorage\Model;

interface ObjectInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getParent();

    /**
     * @return int
     */
    public function getDepth();
}
