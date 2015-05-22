<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database;

use Symcloud\Component\Database\Model\ModelInterface;

interface DatabaseInterface
{
    public function discover($searchPattern);

    public function store(ModelInterface $model);

    public function fetch($hash, $className = null);

    public function fetchProxy($hash, $className);

    public function contains($hash);

    public function delete($hash);

    public function deleteAll();
}
