<?php

namespace Symcloud\Component\MetadataStorage\Model;

class ReferenceObjectModel extends ObjectModel
{
    /**
     * @return boolean
     */
    public function isFile()
    {
        return false;
    }
}
