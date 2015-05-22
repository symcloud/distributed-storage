<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Model\Reference;

use Symcloud\Component\Database\Model\Commit\CommitInterface;
use Symcloud\Component\Database\Model\Model;
use Symfony\Component\Security\Core\User\UserInterface;

class Reference extends Model implements ReferenceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CommitInterface
     */
    private $commit;

    /**
     * @var UserInterface
     */
    private $user;

    public function getHash()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return CommitInterface
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param CommitInterface $commit
     */
    public function setCommit($commit)
    {
        $this->commit = $commit;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }

    /**
     * @param CommitInterface $commit
     */
    public function update(CommitInterface $commit)
    {
        $this->commit = $commit;
    }
}
