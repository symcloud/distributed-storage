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

class NotAFileException extends \Exception
{
    /**
     * @var string string
     */
    private $filePath;

    /**
     * NotAFileException constructor.
     *
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        parent::__construct(sprintf('Path "%s" is not a file', $filePath));

        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }
}
