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

class ReferenceModel implements ReferenceInterface
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $name;

    /**
     * @var CommitInterface
     */
    private $commit;

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return sprintf('%s-%s', $this->user->getUsername(), $this->getName());
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * @param CommitInterface $commit
     */
    public function setCommit(CommitInterface $commit)
    {
        $this->commit = $commit;
    }

    /**
     * {@inheritdoc}
     */
    public function update(CommitInterface $commit)
    {
        $this->commit = $commit;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            self::COMMIT_KEY => $this->getCommit()->getHash(),
            self::USER_KEY => $this->getUser()->getUsername(),
            self::NAME_KEY => $this->getName(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
