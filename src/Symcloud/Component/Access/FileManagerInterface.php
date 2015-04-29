<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Access;

use Symcloud\Component\Access\Exception\NotAFileException;
use Symcloud\Component\Access\Model\FileInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface FileManagerInterface
{
    /**
     * @param string        $path
     * @param UserInterface $user
     *
     * @return FileInterface
     *
     * @throws NotAFileException
     */
    public function getByPath($path, UserInterface $user);
}
