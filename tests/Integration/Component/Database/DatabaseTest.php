<?php

namespace Integration\Component\Database;

use Integration\Parts\DatabaseTrait;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Metadata\ClassMetadata\ClassMetadata;
use Symcloud\Component\Database\Metadata\Field\AccessorField;
use Symcloud\Component\Database\Metadata\Field\ReadonlyAccessorField;
use Symcloud\Component\Database\Metadata\Field\ReferenceArrayField;
use Symcloud\Component\Database\Metadata\Field\ReferenceField;
use Symcloud\Component\Database\Metadata\Field\UserField;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\DistributedModel;
use Symcloud\Component\Database\Model\Model;
use Symcloud\Component\Database\Model\ModelInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DatabaseTest extends ProphecyTestCase
{
    use DatabaseTrait;

    public function dataProvider()
    {
        $b = new B();
        $b->setHash('b-hash');
        $b->setPolicyCollection(new PolicyCollection());
        $b->name = 'b';

        $c = new C();
        $c->setHash('c-hash');
        $c->name = 'c';

        $a = new A();
        $a->setPolicyCollection(new PolicyCollection());
        $a->title = 'a';
        $a->reference = $b;
        $a->references = array($b, $c);
        $a->user = $this->getUserProvider()->loadUserByUsername('johannes');

        $data = array(
            'policies' => array(),
            'data' => array(
                'title' => $a->title,
                'reference' => $a->reference->getHash(),
                'readonly' => $a->getReadonly(),
                'references' => array(
                    array(
                        'hash' => $b->getHash(),
                        'class' => B::class,
                    ),
                    array(
                        'hash' => $c->getHash(),
                        'class' => C::class,
                    ),
                ),
                'user' => $a->user->getUsername(),
            ),
            'class' => A::class,
        );

        return array(array($a, $b, $c, $data, $this->getFactory()->createHash(json_encode($data['data']))));
    }

    /**
     * @dataProvider dataProvider
     *
     * @param A $a
     * @param B $b
     * @param C $c
     * @param $data
     * @param $hash
     */
    public function testStore(A $a, B $b, C $c, $data, $hash)
    {
        $database = $this->getDatabase();
        $database->store($b);
        $database->store($c);

        $result = $database->store($a);

        $this->assertEquals($a, $result);
        $this->assertNotNull($a->getHash());

        $adapter = $this->getStorageAdapter();
        $this->assertTrue($adapter->contains($a->getHash(), 'test'));

        $result = $adapter->fetch($a->getHash(), 'test');
        $this->assertEquals(
            $data,
            $result
        );

        $this->assertEquals($hash, $a->getHash());
    }

    /**
     * @dataProvider dataProvider
     *
     * @param A $a
     * @param B $b
     * @param C $c
     * @param $data
     * @param $hash
     */
    public function testFetchWithClassname(A $a, B $b, C $c, $data, $hash)
    {
        $database = $this->getDatabase();
        $adapter = $this->getStorageAdapter();

        $database->store($b);
        $database->store($c);
        $adapter->store($hash, $data, 'test');

        /** @var A $result */
        $result = $database->fetch($hash, A::class);

        $this->assertEquals($a->title, $result->title);
        $this->assertEquals($a->user, $result->user);
        $this->assertEquals($a->reference->getHash(), $result->reference->getHash());
        $this->assertCount(2, $a->references);
        $this->assertEquals($a->references[0]->getHash(), $result->references[0]->getHash());
        $this->assertEquals($a->references[1]->getHash(), $result->references[1]->getHash());
        $this->assertEquals($a->getReadonly(), $result->getReadonly());
    }

    /**
     * @dataProvider dataProvider
     * @expectedException \Exception
     *
     * @param A $a
     * @param B $b
     * @param C $c
     * @param $data
     * @param $hash
     */
    public function testFetchWithWrongClassname(A $a, B $b, C $c, $data, $hash)
    {
        $database = $this->getDatabase();
        $adapter = $this->getStorageAdapter();
        $adapter->store($hash, $data, A::class);

        $database->fetch($hash, B::class);
    }

    /**
     * @dataProvider dataProvider
     *
     * @param A $a
     * @param B $b
     * @param C $c
     * @param $data
     * @param $hash
     */
    public function testStoreUndistributedModel(A $a, B $b, C $c, $data, $hash)
    {
        $database = $this->getDatabase();
        $adapter = $this->getStorageAdapter();
        $database->store($c, C::class);

        $result = $adapter->fetch($c->getHash(), 'test');
        $this->assertEquals(array('data' => array('name' => 'c'), 'class' => C::class), $result);
    }

    protected function createMetadataManager()
    {
        $aClassMetadata = new AClassMetadata($this->getUserProvider());
        $bClassMetadata = new BClassMetadata();

        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn($aClassMetadata);
        $metadataManager->loadByModel(Argument::type(B::class))->willReturn($bClassMetadata);
        $metadataManager->loadByModel(Argument::type(c::class))->willReturn($bClassMetadata);
        $metadataManager->loadByClassname(A::class)->willReturn($aClassMetadata);
        $metadataManager->loadByClassname(B::class)->willReturn($bClassMetadata);
        $metadataManager->loadByClassname(C::class)->willReturn($bClassMetadata);

        return $metadataManager->reveal();
    }
}

class A extends DistributedModel
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var B
     */
    public $reference;

    /**
     * @var ModelInterface
     */
    public $references;

    /**
     * @var UserInterface
     */
    public $user;

    /**
     * @return string
     */
    public function getReadonly()
    {
        return 'hello';
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return self::class;
    }
}

class B extends DistributedModel
{
    /**
     * @var string
     */
    public $name;

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }
}

class C extends Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @return string
     */
    public function getClass()
    {
        return self::class;
    }
}

class AClassMetadata extends ClassMetadata
{
    /**
     * AClassMetadata constructor.
     * @param UserProviderInterface $userProvider
     */
    public function __construct(UserProviderInterface $userProvider)
    {
        parent::__construct(
            array(
                new AccessorField('title'),
                new ReferenceField('reference', B::class),
                new ReadonlyAccessorField('readonly')
            ),
            array(
                new AccessorField('title'),
                new ReferenceArrayField('references'),
                new UserField('user', $userProvider),
            ),
            'test',
            true
        );
    }
}

class BClassMetadata extends ClassMetadata
{
    /**
     * AClassMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct(
            array(
                new AccessorField('name'),
            ),
            array(),
            'test',
            false
        );
    }
}
