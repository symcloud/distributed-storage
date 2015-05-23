<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Search;

use Symcloud\Component\Database\Metadata\ClassMetadataInterface;
use Symcloud\Component\Database\Model\ModelInterface;

interface SearchAdapterInterface
{
    public function index($hash, ModelInterface $model, ClassMetadataInterface $metadata);

    public function search($query, $contexts = null);

    public function getStatus();

    public function deindex($hash, ClassMetadataInterface $metadata);

    public function deindexAll();
}