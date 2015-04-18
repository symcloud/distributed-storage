<?php

namespace Symcloud\Component\FileStorage;

interface FileAdapterInterface
{
    public function storeFile($getHash, $getBlobs);
}
