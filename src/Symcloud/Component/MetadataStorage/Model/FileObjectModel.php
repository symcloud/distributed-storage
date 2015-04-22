<?php

namespace Symcloud\Component\MetadataStorage\Model;

use Symcloud\Component\FileStorage\Model\FileInterface;

class FileObjectModel extends ObjectModel
{
    /**
     * @var FileInterface
     */
    private $file;

    /**
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param FileInterface $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
