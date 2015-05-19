<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\FileStorage\Model;

interface BlobFileInterface
{
    const BLOBS_KEY = 'blobs';
    const MIME_TYPE_KEY = 'mimetype';
    const SIZE_KEY = 'size';

    /**
     * @return string
     */
    public function getHash();

    /**
     * @return int
     */
    public function getSize();

    /**
     * @return string
     */
    public function getMimeType();

    /**
     * @param int $length
     * @param int $offset
     *
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);

    /**
     * @return array
     */
    public function toArray();
}
