<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

use Symcloud\Component\FileStorage\Model\BlobFileInterface;

interface FileNodeInterface extends NodeInterface
{
    const FILE_KEY = 'file';
    const METADATA_KEY = 'metadata';

    /**
     * @return BlobFileInterface
     */
    public function getFile();

    /**
     * @return MetadataInterface
     */
    public function getMetadata();

    /**
     * @return string
     */
    public function getName();
}
