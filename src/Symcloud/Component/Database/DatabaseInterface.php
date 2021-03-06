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
    public function search($searchPattern, array $contexts = array());

    public function store(ModelInterface $model, array $options = array());

    public function fetch($hash, $className);

    public function fetchProxy($hash, $className);

    public function contains($hash, $className);

    public function delete($hash, $className);

    public function deleteAll();
}
