<?php

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferenceManager implements ReferenceManagerInterface
{

    /**
     * @param UserInterface $user
     * @return ReferenceInterface
     */
    public function getForUser(UserInterface $user)
    {
        // TODO: Implement getForUser() method.
    }
}
