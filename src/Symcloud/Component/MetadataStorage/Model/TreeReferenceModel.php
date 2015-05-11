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

class TreeReferenceModel extends BaseTreeModel implements TreeReferenceInterface
{
    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @var string
     */
    private $referenceName;

    /**
     * @var string
     */
    private $name;

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        $this->setDirty();
    }

    /**
     * {@inheritdoc}
     */
    public function getReferenceName()
    {
        return $this->referenceName;
    }

    /**
     * {@inheritdoc}
     */
    public function setReferenceName($referenceName)
    {
        $this->referenceName = $referenceName;

        $this->setDirty();
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
     * @return array
     */
    protected function toArrayForHash()
    {
        return array(
            self::TYPE_KEY => self::FILE_TYPE,
            self::PATH_KEY => $this->getPath(),
            self::REFERENCE_KEY => array(
                self::USERNAME_KEY => $this->getUser()->getUsername(),
                self::NAME_KEY => $this->getReferenceName(),
            ),
        );
    }

    /**
     * @return string
     */
    public function getType()
    {
        return self::REFERENCE_TYPE;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            self::TYPE_KEY => self::FILE_TYPE,
            self::PATH_KEY => $this->getPath(),
            self::ROOT_KEY => $this->getRoot()->getHash(),
            self::PARENT_KEY => $this->getParent()->getHash(),
            self::REFERENCE_KEY => array(
                self::USERNAME_KEY => $this->getUser()->getUsername(),
                self::NAME_KEY => $this->getReferenceName(),
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->hash = null;
    }
}
