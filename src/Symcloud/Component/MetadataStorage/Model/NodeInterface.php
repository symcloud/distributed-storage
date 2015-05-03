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

interface NodeInterface extends \JsonSerializable
{
    const TYPE_KEY = 'type';
    const ROOT_KEY = 'root';
    const PATH_KEY = 'path';

    /**
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     */
    public function setHash($hash);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return TreeInterface
     */
    public function getRoot();

    /**
     * @return array
     */
    public function toArray();
}
