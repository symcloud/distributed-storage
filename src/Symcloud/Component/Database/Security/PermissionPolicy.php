<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Security;

class PermissionPolicy
{
    private $users = array();

    public function getUsers()
    {
        return $this->users;
    }

    public function addUser($username, $permissions)
    {
        $this->users[$username] = $permissions;
    }

    public function getPermissions($username)
    {
        return $this->users[$username];
    }
}
