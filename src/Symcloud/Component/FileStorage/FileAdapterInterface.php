<?php

namespace Symcloud\Component\FileStorage;

interface FileAdapterInterface
{
    /**
     * @param string $hash
     * @param string[] $blobs
     * @return boolean
     */
    public function storeFile($hash, $blobs);

    /**
     * @param string $hash
     * @return boolean
     */
    public function fileExists($hash);

    /**
     * @param string $hash
     * @return string[]
     */
    public function fetchFile($hash);
}
