<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Reference;

use Symcloud\Component\MetadataStorage\Model\ReferenceInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceAdapterInterface
{
    /**
     * @param ReferenceInterface $reference
     */
    public function storeReference(ReferenceInterface $reference);

    /**
     * @param UserInterface $user
     * @param string        $name
     *
     * @return array
     */
    public function fetchReferenceData(UserInterface $user, $name = 'HEAD');

    /**
     * @param string $username
     * @param string $name
     *
     * @return array
     */
    public function fetchReferenceDataByUsername($username, $name = 'HEAD');
}
