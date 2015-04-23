<?php
namespace Symcloud\Component\FileStorage;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\FileObjectInterface;

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

    /**
     * @param FileObjectInterface $object
     * @return BlobFileInterface
     */
    public function downloadByObject(FileObjectInterface $object);
}
