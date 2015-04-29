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

interface ObjectInterface
{
    /**
     * @return string
     */
    public function getHash();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getParent();

    /**
     * @return int
     */
    public function getDepth();

    /**
     * @return bool
     */
    public function isFile();
}
