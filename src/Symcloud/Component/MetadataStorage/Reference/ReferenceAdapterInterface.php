<?php

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceAdapterInterface
{
    /**
     * @param ReferenceInterface $reference
     * @return boolean
     */
    public function storeReference(ReferenceInterface $reference);

    /**
     * @param UserInterface $user
     * @param string $name
     * @return array
     */
    public function fetchReference(UserInterface $user, $name = 'HEAD');
}
