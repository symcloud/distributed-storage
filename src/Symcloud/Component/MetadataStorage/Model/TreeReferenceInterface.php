<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\MetadataStorage\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface TreeReferenceInterface extends NodeInterface
{
    const USERNAME_KEY = 'username';
    const NAME_KEY = 'name';
    const REFERENCE_KEY = 'reference';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getReferenceName();

    /**
     * @param string $referenceName
     */
    public function setReferenceName($referenceName);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);
}
