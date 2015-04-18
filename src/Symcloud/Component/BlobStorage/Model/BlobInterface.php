<?php
namespace Symcloud\Component\BlobStorage\Model;

interface BlobInterface
{
    /**
     * @return string
     */
    public function getData();

    /**
     * @param string $data
     */
    public function setData($data);
}
