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

interface FileInterface
{
    const TITLE = 'title';
    const DESCRIPTION = 'description';

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getMetadata($name);

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
     *
     * @return mixed
     */
    public function getContent($length = -1, $offset = 0);
}
