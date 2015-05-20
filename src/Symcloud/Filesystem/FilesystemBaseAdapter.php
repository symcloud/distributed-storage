<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Filesystem;

use Doctrine\Common\Cache\FilesystemCache;
use Symcloud\Component\Common\AdapterInterface;

class FilesystemBaseAdapter extends FilesystemCache implements AdapterInterface
{
    /**
     * {@inheritdoc}
     */
    public function remove($hash)
    {
        return $this->delete($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchHashes()
    {
        $result = array();
        $iterator = new \RegexIterator(
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->directory)),
            '/^.+' . preg_quote($this->getExtension(), '/') . '$/i'
        );

        foreach ($iterator as $file) {
            $matches = array();
            preg_match('/^\[(\w*)\]\[(\d*)\]' . $this->getExtension() . '$/i', $file->getFileName(), $matches);

            if (sizeof($matches) > 1) {
                $result[] = $matches[1];
            }
        }

        return $result;
    }
}
