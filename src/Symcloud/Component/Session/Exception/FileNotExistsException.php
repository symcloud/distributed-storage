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

class FileNotExistsException extends \Exception
{
    /**
     * @var string
     */
    private $path;

    /**
     * FileNotExistsException constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(sprintf('Path "%s" does not exists', $path));

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
