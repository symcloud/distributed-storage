<?php

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceManagerInterface
{
    /**
     * @param UserInterface $user
     * @return ReferenceInterface
     */
    public function getForUser(UserInterface $user);
}
