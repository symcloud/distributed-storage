<?php
namespace Symcloud\Component\Access\Model;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;
use Symcloud\Component\MetadataStorage\Model\KeyValueInterface;

interface FileInterface
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @return KeyValueInterface
     */
    public function getMetadataStore();

    /**
     * @return string
     */
    public function getFileHash();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return int
     */
    public function getDepth();

    /**
     * @param int $length
     * @param int $offset
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);

    /**
     * @param BlobFileInterface $data
     */
    public function setData($data);
}
