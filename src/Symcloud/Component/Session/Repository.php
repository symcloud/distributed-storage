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

class Repository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function login(UserInterface $user, $reference = 'HEAD')
    {
        // TODO: Implement login() method.
    }
}
