<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Session;

use Symfony\Component\Security\Core\User\UserInterface;

interface RepositoryInterface
{
    /**
     * @param UserInterface $user
     * @param string $reference
     *
     * @return SessionInterface
     */
    public function login(UserInterface $user, $reference = 'HEAD');
}
