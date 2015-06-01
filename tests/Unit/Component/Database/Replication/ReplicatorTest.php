<?php

namespace Unit\Component\Database\Replication;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Message\RequestInterface;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Event\DatabaseFetchEvent;
use Symcloud\Component\Database\Event\DatabaseStoreEvent;
use Symcloud\Component\Database\Metadata\ClassMetadata\ClassMetadata;
use Symcloud\Component\Database\Metadata\Field\AccessorField;
use Symcloud\Component\Database\Metadata\MetadataManagerInterface;
use Symcloud\Component\Database\Model\DistributedModel;
use Symcloud\Component\Database\Model\DistributedModelInterface;
use Symcloud\Component\Database\Model\PolicyCollection;
use Symcloud\Component\Database\Model\PolicyCollectionInterface;
use Symcloud\Component\Database\Replication\ApiInterface;
use Symcloud\Component\Database\Replication\Exception\NotPrimaryServerException;
use Symcloud\Component\Database\Replication\Replicator;
use Symcloud\Component\Database\Replication\ReplicatorInterface;
use Symcloud\Component\Database\Replication\ReplicatorPolicy;
use Symcloud\Component\Database\Replication\Server;
use Symcloud\Component\Database\Search\SearchAdapterInterface;
use Symcloud\Component\Database\Storage\StorageAdapterInterface;

class ReplicatorTest extends ProphecyTestCase
{
    public function testOnStoreFull()
    {
        $hash = '123-123-123';
        $model = new A();
        $model->setHash($hash);
        $model->name = 'HELLO';
        $data = array('name' => $model->name);
        $event = new DatabaseStoreEvent(
            $model,
            $data,
            true,
            new AClassMetadata(),
            array(ReplicatorInterface::OPTION_NAME => ReplicatorInterface::TYPE_FULL)
        );

        $primaryServer = new Server('my.symcloud.lo');
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        $api = $this->prophesize(ApiInterface::class);
        $api->fetch()->shouldNotBeCalled();
        $api->store(
            $hash,
            Argument::that(
                function ($argument) use ($model, $primaryServer) {
                    $this->assertEquals($argument['type'], 'backup');
                    $this->assertEquals($argument['class'], A::class);
                    $this->assertEquals($argument['data'], array('name' => $model->name));

                    /** @var PolicyCollectionInterface $policyCollection */
                    $policyCollection = unserialize($argument['policies']);
                    $this->assertInstanceOf(PolicyCollectionInterface::class, $policyCollection);
                    $this->assertTrue($policyCollection->has(Replicator::POLICY_NAME));

                    /** @var ReplicatorPolicy $policy */
                    $policy = $policyCollection->get(Replicator::POLICY_NAME);
                    $this->assertInstanceOf(ReplicatorPolicy::class, $policy);
                    $this->assertEquals($primaryServer, $policy->getPrimaryServer());
                    $this->assertCount(2, $policy->getBackupServers());

                    return true;
                }
            ),
            Argument::that(
                function ($argument) use ($servers) {
                    $this->assertContains($argument->getHost(), $servers);

                    return true;
                }
            )
        )->shouldBeCalledTimes(2);

        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn(new AClassMetadata());
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn(new AClassMetadata());

        /** @var StorageAdapterInterface $storageAdapter */
        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);

        /** @var SearchAdapterInterface $searchAdapter */
        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        $replicator->addServer($servers[0]);
        $replicator->addServer($servers[1]);
        $replicator->addServer($servers[2]);
        $replicator->onStore($event);

        /** @var DistributedModelInterface $model */
        $model = $event->getModel();
        $policyCollection = $model->getPolicyCollection();
        $this->assertTrue($policyCollection->has(Replicator::POLICY_NAME));

        /** @var ReplicatorPolicy $policy */
        $policy = $model->getPolicyCollection()->get(Replicator::POLICY_NAME);
        $this->assertInstanceOf(ReplicatorPolicy::class, $policy);
        $this->assertEquals($primaryServer, $policy->getPrimaryServer());

        $this->assertCount(2, $policy->getBackupServers());
        $this->assertContains($policy->getBackupServers()[0]->getHost(), $servers);
        $this->assertContains($policy->getBackupServers()[1]->getHost(), $servers);
    }

    public function testOnStoreNone()
    {
        $hash = '123-123-123';
        $model = new A();
        $model->setHash($hash);
        $model->name = 'HELLO';
        $data = array('name' => $model->name);
        $event = new DatabaseStoreEvent(
            $model,
            $data,
            true,
            new AClassMetadata(),
            array(ReplicatorInterface::OPTION_NAME => ReplicatorInterface::TYPE_NONE)
        );

        $api = $this->prophesize(ApiInterface::class);
        $api->fetch()->shouldNotBeCalled();
        $api->store()->shouldNotBeCalled();

        $primaryServer = new Server('my.symcloud.lo');
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        /** @var MetadataManagerInterface $metadataManager */
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn(new AClassMetadata());
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn(new AClassMetadata());

        /** @var StorageAdapterInterface $storageAdapter */
        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);

        /** @var SearchAdapterInterface $searchAdapter */
        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        array_map(
            function ($server) use ($replicator) {
                $replicator->addServer($server);
            },
            $servers
        );

        $replicator->onStore($event);

        /** @var DistributedModelInterface $model */
        $model = $event->getModel();
        $policyCollection = $model->getPolicyCollection();
        $this->assertFalse($policyCollection->has(Replicator::POLICY_NAME));
    }

    public function testStore()
    {
        $hash = 'my-hash';
        $object = array(
            'type' => 'backup',
            'class' => A::class,
            'data' => array(
                'name' => 'HELLO'
            ),
            'policies' => serialize(new PolicyCollection()),
        );

        $api = $this->prophesize(ApiInterface::class);

        $classMetadata = new AClassMetadata();
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn($classMetadata);
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn($classMetadata);

        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);
        $storageAdapter->store($hash, $object, $classMetadata->getContext())->shouldBeCalled()->willReturn(true);

        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);
        $searchAdapter->indexObject($hash, $object['data'], $classMetadata)->shouldBeCalled()->willReturn(true);

        $primaryServer = new Server('my.symcloud.lo');
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        array_map(
            function ($server) use ($replicator) {
                $replicator->addServer($server);
            },
            $servers
        );

        $replicator->store($hash, $object);
    }

    public function testFetch()
    {
        $hash = 'my-hash';
        $object = array(
            'class' => A::class,
            'data' => array(
                'name' => 'HELLO'
            ),
            'policies' => serialize(new PolicyCollection()),
        );

        $api = $this->prophesize(ApiInterface::class);

        $classMetadata = new AClassMetadata();
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn($classMetadata);
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn($classMetadata);

        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);
        $storageAdapter->store()->shouldNotBeCalled();
        $storageAdapter->fetch($hash, $classMetadata->getContext())->willReturn($object);
        $storageAdapter->contains($hash, A::class)->willReturn(true);

        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);
        $searchAdapter->indexObject()->shouldNotBeCalled();

        $primaryServer = new Server('my.symcloud.lo', 1234);
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        array_map(
            function ($server) use ($replicator) {
                $replicator->addServer($server);
            },
            $servers
        );

        $result = $replicator->fetch($hash, A::class, 'my.symcloud.lo::johannes');
        $this->assertEquals($object, $result);
    }

    public function testFetchFromBackup()
    {
        $this->setExpectedExceptionRegExp(NotPrimaryServerException::class, '/.*your-1.symcloud.lo:80.*/');

        $primaryServer = new Server('my.symcloud.lo');
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        $hash = 'my-hash';
        $object = array(
            'type' => 'backup',
            'class' => A::class,
            'data' => array(
                'name' => 'HELLO',
            ),
            'policies' => serialize(
                new PolicyCollection(
                    array(
                        'replicator' => new ReplicatorPolicy(
                            new Server($servers[0]),
                            array($primaryServer, new Server($servers[1]))
                        )
                    )
                )
            ),
        );

        $api = $this->prophesize(ApiInterface::class);

        $classMetadata = new AClassMetadata();
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn($classMetadata);
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn($classMetadata);

        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);
        $storageAdapter->store()->shouldNotBeCalled();
        $storageAdapter->fetch($hash, $classMetadata->getContext())->willReturn($object);
        $storageAdapter->contains($hash, A::class)->willReturn(true);

        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);
        $searchAdapter->indexObject()->shouldNotBeCalled();

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        array_map(
            function ($server) use ($replicator) {
                $replicator->addServer($server);
            },
            $servers
        );

        $replicator->fetch($hash, A::class, 'my.symcloud.lo::johannes');
    }

    public function testOnFetchNoData()
    {
        $primaryServer = new Server('my.symcloud.lo');
        $servers = array(
            'your-1.symcloud.lo',
            'your-2.symcloud.lo',
            'your-3.symcloud.lo',
        );

        $hash = 'my-hash';
        $object = array(
            'class' => A::class,
            'data' => array(
                'name' => 'HELLO',
            ),
            'policies' => serialize(
                new PolicyCollection(
                    array(
                        'replicator' => new ReplicatorPolicy(
                            new Server($servers[0]),
                            array($primaryServer, new Server($servers[1]))
                        )
                    )
                )
            ),
        );

        $request = $this->prophesize(RequestInterface::class);

        $api = $this->prophesize(ApiInterface::class);
        $api->fetch($hash, A::class, new Server($servers[0]))
            ->shouldBeCalled()
            ->willThrow(new ClientException('Not found 1', $request->reveal()));

        $api->fetch($hash, A::class, new Server($servers[1]))
            ->shouldBeCalled()
            ->willThrow(new ClientException('Not found 2', $request->reveal()));

        $api->fetch($hash, A::class, new Server($servers[2]))
            ->shouldBeCalled()
            ->willReturn($object);

        $classMetadata = new AClassMetadata();
        $metadataManager = $this->prophesize(MetadataManagerInterface::class);
        $metadataManager->loadByClassname(A::class)->willReturn($classMetadata);
        $metadataManager->loadByModel(Argument::type(A::class))->willReturn($classMetadata);

        $storageAdapter = $this->prophesize(StorageAdapterInterface::class);
        $storageAdapter->store()->shouldNotBeCalled();
        $storageAdapter->fetch()->shouldNotBeCalled();

        $searchAdapter = $this->prophesize(SearchAdapterInterface::class);
        $searchAdapter->indexObject()->shouldNotBeCalled();

        $replicator = new Replicator(
            $api->reveal(),
            $storageAdapter->reveal(),
            $searchAdapter->reveal(),
            $metadataManager->reveal(),
            $primaryServer
        );
        array_map(
            function ($server) use ($replicator) {
                $replicator->addServer($server);
            },
            $servers
        );

        $event = new DatabaseFetchEvent($hash, null, A::class, $classMetadata);
        $replicator->onFetch($event);

        $this->assertEquals($object, $event->getData());
    }
}

class A extends DistributedModel
{
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
     */
    public function __construct()
    {
        parent::__construct(
            array(new AccessorField('name')),
            array(),
            'test',
            true
        );
    }
}
