<?php

namespace Integration\Component\Reference;

use Integration\Parts\ReferenceManagerTrait;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Model\Commit\Commit;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\Database\Model\Reference\Reference;
use Symcloud\Component\Database\Model\Reference\ReferenceInterface;
use Symcloud\Component\Database\Model\Tree\Tree;
use Symfony\Component\Security\Core\User\UserInterface;

class ReferenceManagerTest extends ProphecyTestCase
{
    use ReferenceManagerTrait;

    public function dataProvider()
    {
        $username = 'johannes';
        $message = 'My message';

        $user = $this->getUserProvider()->loadUserByUsername($username);
        $tree = new Tree();
        $tree->setPolicyCollection(new PolicyCollection());
        $tree->setName('');
        $tree->setPath('/');

        $commit = new Commit();
        $commit->setPolicyCollection(new PolicyCollection());
        $commit->setMessage('init');
        $commit->setCommitter($user);
        $commit->setCreatedAt(new \DateTime());
        $commit->setTree($tree);
        $commit->setParentCommit(null);

        $commit2 = new Commit();
        $commit2->setPolicyCollection(new PolicyCollection());
        $commit2->setMessage($message);
        $commit2->setCommitter($user);
        $commit2->setCreatedAt(new \DateTime());
        $commit2->setTree($tree);
        $commit2->setParentCommit($commit);

        return array(array('my-hash', $commit, $commit2, $tree, $user));
    }

    /**
     * @dataProvider dataProvider
     * @param string $name
     * @param Commit $commit
     * @param Commit $commit2
     * @param Tree $tree
     * @param UserInterface $user
     */
    public function testFetch(
        $name,
        Commit $commit,
        Commit $commit2,
        Tree $tree,
        UserInterface $user
    ) {
        $hash = '123-123-123';
        $database = $this->getDatabase();

        $database->store($tree);
        $database->store($commit);

        $reference = new Reference();
        $reference->setHash($hash);
        $reference->setCommit($commit);
        $reference->setUser($user);
        $reference->setName($name);
        $database->store($reference);

        $referenceManager = $this->getReferenceManager();
        $reference = $referenceManager->fetch($hash);

        $this->assertEquals($hash, $reference->getHash());
        $this->assertEquals($name, $reference->getName());
        $this->assertEquals($commit->getHash(), $reference->getCommit()->getHash());
        $this->assertEquals($user->getUsername(), $reference->getUser()->getUsername());

        /** @var ReferenceInterface $result */
        $result = $database->fetch($hash, Reference::class);

        $this->assertEquals($hash, $reference->getHash());
        $this->assertEquals($name, $reference->getName());
        $this->assertEquals($commit->getHash(), $result->getCommit()->getHash());
        $this->assertEquals($user->getUsername(), $result->getUser()->getUsername());
    }

    /**
     * @dataProvider dataProvider
     * @param string $name
     * @param Commit $commit
     * @param Commit $commit2
     * @param Tree $tree
     * @param UserInterface $user
     */
    public function testUpdateReference(
        $name,
        Commit $commit,
        Commit $commit2,
        Tree $tree,
        UserInterface $user
    ) {
        $hash = '123-123-123';
        $database = $this->getDatabase();

        $database->store($tree);
        $database->store($commit);
        $database->store($commit2);

        $reference = new Reference();
        $reference->setHash($hash);
        $reference->setCommit($commit);
        $reference->setUser($user);
        $reference->setName($name);
        $database->store($reference);

        $referenceManager = $this->getReferenceManager();
        $reference2 = $referenceManager->update($reference, $commit2);

        $this->assertEquals($reference2, $reference);

        /** @var ReferenceInterface $result */
        $result = $database->fetch($hash, Reference::class);

        $this->assertEquals($hash, $result->getHash());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($commit2->getHash(), $result->getCommit()->getHash());
        $this->assertEquals($user->getUsername(), $result->getUser()->getUsername());
    }

    /**
     * @dataProvider dataProvider
     * @param string $name
     * @param Commit $commit
     * @param Commit $commit2
     * @param Tree $tree
     * @param UserInterface $user
     */
    public function testCreateReference(
        $name,
        Commit $commit,
        Commit $commit2,
        Tree $tree,
        UserInterface $user
    ) {
        $database = $this->getDatabase();

        $database->store($tree);
        $database->store($commit);

        $referenceManager = $this->getReferenceManager();
        $reference = $referenceManager->create($name, $user, $commit);

        $referenceHash = $referenceManager->createHash($user, $name);

        $this->assertEquals($referenceHash, $reference->getHash());
        $this->assertEquals($name, $reference->getName());
        $this->assertEquals($commit->getHash(), $reference->getCommit()->getHash());
        $this->assertEquals($user->getUsername(), $reference->getUser()->getUsername());

        /** @var ReferenceInterface $result */
        $result = $database->fetch($referenceHash, Reference::class);

        $this->assertEquals($referenceHash, $result->getHash());
        $this->assertEquals($name, $result->getName());
        $this->assertEquals($commit->getHash(), $result->getCommit()->getHash());
        $this->assertEquals($user->getUsername(), $result->getUser()->getUsername());
    }
}
