<?php
namespace Symcloud\Component\FileStorage;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;

interface BlobFileManagerInterface
{
    /**
     * @param string $filePath
     * @return BlobFileInterface
     */
    public function upload($filePath);

    /**
     * @param string $fileHash
     * @return BlobFileInterface
     */
    public function download($fileHash);
}
