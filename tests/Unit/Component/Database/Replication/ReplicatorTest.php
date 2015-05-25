<?php

namespace Unit\Component\Database\Replication;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTestCase;
use Symcloud\Component\Database\Event\DatabaseStoreEvent;
use Symcloud\Component\Database\Metadata\ClassMetadata\ClassMetadata;
use Symcloud\Component\Database\Metadata\Field\AccessorField;
use Symcloud\Component\Database\Model\DistributedModel;
use Symcloud\Component\Database\Model\DistributedModelInterface;
use Symcloud\Component\Database\Model\PolicyCollectionInterface;
use Symcloud\Component\Database\Replication\ApiInterface;
use Symcloud\Component\Database\Replication\Replicator;
use Symcloud\Component\Database\Replication\ReplicatorInterface;
use Symcloud\Component\Database\Replication\ReplicatorPolicy;
use Symcloud\Component\Database\Replication\Server;

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
            new Server('your-1.symcloud.lo'),
            new Server('your-2.symcloud.lo'),
            new Server('your-3.symcloud.lo'),
        );

        $api = $this->prophesize(ApiInterface::class);
        $api->fetch()->shouldNotBeCalled();
        $api->store(
            $hash,
            Argument::that(
                function ($argument) use ($model, $primaryServer, $servers) {
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
                    $this->assertContains($argument, $servers);

                    return true;
                }
            )
        )->shouldBeCalledTimes(2);

        $replicator = new Replicator($api->reveal(), $primaryServer, $servers);
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
        $this->assertContains($policy->getBackupServers()[0], $servers);
        $this->assertContains($policy->getBackupServers()[1], $servers);
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

        $primaryServer = new Server('my.symcloud.lo', 1234);
        $servers = array(
            new Server('your-1.symcloud.lo', 1234),
            new Server('your-2.symcloud.lo', 1234),
            new Server('your-3.symcloud.lo', 1234)
        );

        $replicator = new Replicator($api->reveal(), $primaryServer, $servers);
        $replicator->onStore($event);

        /** @var DistributedModelInterface $model */
        $model = $event->getModel();
        $policyCollection = $model->getPolicyCollection();
        $this->assertFalse($policyCollection->has(Replicator::POLICY_NAME));
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
