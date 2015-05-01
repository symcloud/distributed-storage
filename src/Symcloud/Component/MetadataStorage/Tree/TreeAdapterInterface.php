<?php

namespace Symcloud\Component\MetadataStorage\Tree;

interface TreeAdapterInterface
{
    /**
     * @param string $hash
     * @param array $data
     * @return boolean
     */
    public function store($hash, $data);

    /**
     * @param string $hash
     * @return array
     */
    public function fetch($hash);
}
