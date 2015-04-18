<?php

namespace Symcloud\Component\FileStorage\Model;

interface FileInterface
{
    /**
     * @return mixed
     */
    public function getBlobs();

    /**
     * @return mixed
     */
    public function getHash();
}
