<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata;

use Symcloud\Component\Database\Model\Blob;
use Symcloud\Component\Database\Model\BlobFile;

class MetadataManager implements MetadataManagerInterface
{
    /**
     * @var ClassMetadataInterface[]
     */
    public $metadata;

    /**
     * MetadataManager constructor.
     */
    public function __construct()
    {
        $this->metadata = array(
            Blob::class => new BlobClassMetadata(),
            BlobFile::class => new BlobFileClassMetadata(),
        );
    }

    public function loadByObject($object)
    {
        return $this->loadByClassname(get_class($object));
    }

    public function loadByClassname($className)
    {
        return $this->metadata[$className];
    }
}
