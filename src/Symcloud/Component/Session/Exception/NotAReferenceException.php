<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Session\Exception;

class NotAReferenceException extends \Exception
{
    /**
     * @var string string
     */
    private $path;

    /**
     * NotAReferenceException constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(sprintf('Path "%s" is not a reference', $path));

        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
