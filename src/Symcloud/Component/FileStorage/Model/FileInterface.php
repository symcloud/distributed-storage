<?php

namespace Symcloud\Component\FileStorage\Model;

interface FileInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @param int $length
     * @param int $offset
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);
}
