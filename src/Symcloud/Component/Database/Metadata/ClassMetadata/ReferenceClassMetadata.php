<?php

/*
 * This file is part of the Symcloud Distributed-Storage.
 *
 * (c) Symcloud and Johannes Wachter
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symcloud\Component\Database\Metadata\ClassMetadata;

use Symcloud\Component\Database\Metadata\Field\AccessorField;
use Symcloud\Component\Database\Metadata\Field\ReferenceField;
use Symcloud\Component\Database\Metadata\Field\UserField;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class ReferenceClassMetadata extends ClassMetadata
{
    /**
     * ReferenceClassMetadata constructor.
     *
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        parent::__construct(
            array(
                new ReferenceField('commit', Commit::class),
                new UserField('user', $userProvider),
                new AccessorField('name'),
            ),
            array(
                new ReferenceField('commit', Commit::class),
                new UserField('user', $userProvider),
                new AccessorField('name'),
            ),
            'reference',
            false
        );
    }
}
