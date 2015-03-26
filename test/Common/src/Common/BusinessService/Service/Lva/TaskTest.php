<?php

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\BusinessService\Service\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\BusinessService\Service\Lva\Task;
use Common\BusinessService\Response;
use CommonTest\Bootstrap;

/**
 * Task Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TaskTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    protected $brm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->brm = m::mock('\Common\BusinessRule\BusinessRuleManager')->makePartial();

        $this->sut = new Task();

        $this->sut->setServiceLocator($this->sm);
        $this->sut->setBusinessRuleManager($this->brm);
    }

    public function testProcess()
    {
        // Data
        $params = [
            'category' => 'foo',
            'foo' => 'bar'
        ];
        $expectedSaveData = [
            'category' => 'foo',
            'foo' => 'bar',
            'user' => 'bar'
        ];

        // Mocks
        $taskRule = m::mock('\Common\BusinessRule\BusinessRuleInterface');
        $this->brm->setService('Task', $taskRule);

        $mockTaskProcessing = m::mock();
        $mockTaskEntity = m::mock();
        $this->sm->setService('Processing\Task', $mockTaskProcessing);
        $this->sm->setService('Entity\Task', $mockTaskEntity);

        // Expectations
        $taskRule->shouldReceive('validate')
            ->with($params)
            ->andReturn($params);

        $mockTaskProcessing->shouldReceive('getAssignment')
            ->with(['category' => 'foo'])
            ->andReturn(['user' => 'bar']);

        $mockTaskEntity->shouldReceive('save')
            ->with($expectedSaveData)
            ->andReturn(['id' => 123]);

        $response = $this->sut->process($params);

        $this->assertInstanceOf('\Common\BusinessService\Response', $response);
        $this->assertEquals(Response::TYPE_SUCCESS, $response->getType());
        $this->assertEquals(['id' => 123], $response->getData());
    }
}
