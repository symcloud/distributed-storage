<?php
namespace Symcloud\Component\MetadataStorage\Model;

use Symfony\Component\Security\Core\User\UserInterface;

interface ReferenceInterface extends \JsonSerializable
{
    const COMMIT_KEY = 'commit';
    const USER_KEY = 'user';
    const NAME_KEY = 'name';

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return CommitInterface
     */
    public function getCommit();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param CommitInterface $commit
     */
    public function update(CommitInterface $commit);
}
