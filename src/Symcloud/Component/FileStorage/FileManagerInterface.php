<?php
namespace Symcloud\Component\FileStorage;

use Symcloud\Component\FileStorage\Model\FileInterface;

interface FileManagerInterface
{
    /**
     * @param string $filePath
     * @return FileInterface
     */
    public function upload($filePath);

    /**
     * @param string $fileHash
     * @return FileInterface
     */
    public function download($fileHash);
}
