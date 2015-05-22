<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Common;

interface FactoryInterface
{
    /**
     * @param $data
     *
     * @return string
     */
    public function createHash($data);

    /**
     * @param string $filePath
     *
     * @return string
     */
    public function createFileHash($filePath);

    /**
     * @param string   $className
     * @param callable $initializerCallback
     *
     * @return mixed
     */
    public function createProxy($className, callable $initializerCallback);
}
