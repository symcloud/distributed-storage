<?php
namespace Symcloud\Component\BlobStorage\Model;

interface BlobInterface
{
    /**
     * @return string
     */
    public function getData();

    /**
     * @return string
     */
    public function getHash();
}
