<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\BlobStorage\Exception;

class BlobNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private $hash;

    /**
     * BlobNotFoundException constructor.
     *
     * @param $hash
     */
    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }
}
