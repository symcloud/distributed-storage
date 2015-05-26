<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Replication;

interface ServerInterface extends \Serializable
{
    /**
     * @return string
     */
    public function getHost();

    /**
     * @return int
     */
    public function getPort();

    /**
     * @param string $path
     *
     * @return string
     */
    public function getUrl($path);
}
