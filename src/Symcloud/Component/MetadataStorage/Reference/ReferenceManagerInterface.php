<?php

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;

interface ReferenceManagerInterface
{
    /**
     * @param $user
     * @return ReferenceInterface
     */
    public function getForUser($user);
}
