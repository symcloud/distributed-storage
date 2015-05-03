<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Access\Model;

use Symcloud\Component\MetadataStorage\Model\TreeFileInterface;

class FileModel implements FileInterface
{
    /**
     * @var TreeFileInterface
     */
    private $treeFile;

    /**
     * @return TreeFileInterface
     */
    public function getTreeFile()
    {
        return $this->treeFile;
    }

    /**
     * @param TreeFileInterface $treeFile
     */
    public function setTreeFile($treeFile)
    {
        $this->treeFile = $treeFile;
    }

    public function getTitle()
    {
        return $this->treeFile->getMetadata(self::TITLE);
    }

    public function getDescription()
    {
        return $this->treeFile->getMetadata(self::DESCRIPTION);
    }

    public function getMetadata($name)
    {
        return $this->treeFile->getMetadata($name);
    }

    public function getFileHash()
    {
        return $this->treeFile->getFile()->getHash();
    }

    public function getPath()
    {
        return $this->treeFile->getPath();
    }

    public function getDepth()
    {
        // TODO calculate depth
    }

    public function getContent($length = -1, $offset = 0)
    {
        return $this->treeFile->getFile()->getContent($length, $offset);
    }
}
