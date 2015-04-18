<?php

namespace Symcloud\Component\FileStorage;

interface FileSplitterInterface
{
    /**
     * @param string $filePath
     * @param callback $callback
     */
    public function split($filePath, $callback);
}
