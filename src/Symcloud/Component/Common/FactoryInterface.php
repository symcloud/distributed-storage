<?php

namespace Symcloud\Component\Common;

use Symcloud\Component\BlobStorage\Model\BlobInterface;
use Symcloud\Component\BlobStorage\Model\BlobModel;
use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\FileStorage\Model\BlobFileModel;

interface FactoryInterface
{
    /**
     * @param string $data
     * @param string|null $hash
     * @return BlobInterface
     */
    public function createBlob($data, $hash = null);

    /**
     * @param $data
     * @return string
     */
    public function createHash($data);

    /**
     * @param string $filePath
     * @return string
     */
    public function createFileHash($filePath);

    /**
     * @param string $hash
     * @param BlobInterface[] $blobs
     * @return BlobFileInterface
     */
    public function createBlobFile($hash, $blobs = array());
}
