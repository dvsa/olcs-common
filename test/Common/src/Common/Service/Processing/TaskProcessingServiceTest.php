<?php

/**
 * Task Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use Common\Service\Processing\TaskProcessingService;
use Common\Service\Data\CategoryDataService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Task Entity Processing Service Test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class TaskProcessingServiceTest extends MockeryTestCase
{
    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new TaskProcessingService();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testWithInvalidData()
    {
        try {
            $this->sut->getAssignment([]);
        } catch (\InvalidArgumentException $ex) {
            $this->assertEquals('Input data is missing required "category" key', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testWithNoCategoryFoundFetchesDefaultAllocation()
    {
        $this->sm->setService(
            'Entity\Category',
            m::mock()
            ->shouldReceive('findById')
            ->with(1234)
            ->andReturn(false)
            ->getMock()
        );

        $this->sm->setService(
            'Entity\SystemParameter',
            m::mock()
            ->shouldReceive('getValue')
            ->with('task.default_team')
            ->andReturn(4321)
            ->shouldReceive('getValue')
            ->with('task.default_user')
            ->andReturn(5678)
            ->getMock()
        );

        $assignment = $this->sut->getAssignment(['category' => 1234]);

        $this->assertEquals(
            [
                'assignedToTeam' => 4321,
                'assignedToUser' => 5678
            ],
            $assignment
        );
    }

    public function testWhenRuleTypeIsNotSimple()
    {
        $this->sm->setService(
            'Entity\Category',
            m::mock()
            ->shouldReceive('findById')
            ->with(1234)
            ->andReturn(
                [
                    'taskAllocationType' => [
                        'id' => 'some_fake_invalid_type'
                    ]
                ]
            )
            ->getMock()
        );

        try {
            $this->sut->getAssignment(['category' => 1234]);
        } catch (\LogicException $ex) {
            $this->assertEquals('Querying for rule type "some_fake_invalid_type" is not supported', $ex->getMessage());
            return;
        }

        $this->fail('Expected exception not raised');
    }

    public function testWithValidRuleTypeAndOneMatchingRule()
    {
        $this->sm->setService(
            'Entity\Category',
            m::mock()
            ->shouldReceive('findById')
            ->with(1234)
            ->andReturn(
                [
                    'taskAllocationType' => [
                        'id' => 'task_at_simple'
                    ]
                ]
            )
            ->getMock()
        );

        $this->sm->setService(
            'Entity\TaskAllocationRule',
            m::mock()
            ->shouldReceive('findByQuery')
            ->with(
                [
                    'category' => 1234,
                    'isMlh' => 'NULL',
                    'trafficArea' => 'NULL'
                ]
            )
            ->andReturn(
                [
                    'Results' => [
                        [
                            'team' => [
                                'id' => 4444
                            ],
                            'user' => [
                                'id' => 5555
                            ]
                        ]
                    ],
                    'Count' => 1
                ]
            )
            ->getMock()
        );

        $assignment = $this->sut->getAssignment(['category' => 1234]);

        $this->assertEquals(
            [
                'assignedToTeam' => 4444,
                'assignedToUser' => 5555
            ],
            $assignment
        );
    }

    public function testWithValidRuleTypeAndNoMatchingRule()
    {
        $this->sm->setService(
            'Entity\Category',
            m::mock()
            ->shouldReceive('findById')
            ->with(1234)
            ->andReturn(
                [
                    'taskAllocationType' => [
                        'id' => 'task_at_simple'
                    ]
                ]
            )
            ->getMock()
        );

        $this->sm->setService(
            'Entity\TaskAllocationRule',
            m::mock()
            ->shouldReceive('findByQuery')
            ->with(
                [
                    'category' => 1234,
                    'isMlh' => 'NULL',
                    'trafficArea' => 'NULL'
                ]
            )
            ->andReturn(
                [
                    'Count' => 0
                ]
            )
            ->getMock()
        );

        $this->sm->setService(
            'Entity\SystemParameter',
            m::mock()
            ->shouldReceive('getValue')
            ->with('task.default_team')
            ->andReturn(4321)
            ->shouldReceive('getValue')
            ->with('task.default_user')
            ->andReturn(5678)
            ->getMock()
        );

        $assignment = $this->sut->getAssignment(['category' => 1234]);

        $this->assertEquals(
            [
                'assignedToTeam' => 4321,
                'assignedToUser' => 5678
            ],
            $assignment
        );
    }

    public function testWithValidRuleTypeAndMultipleMatchingRules()
    {
        $this->sm->setService(
            'Entity\Category',
            m::mock()
            ->shouldReceive('findById')
            ->with(1234)
            ->andReturn(
                [
                    'taskAllocationType' => [
                        'id' => 'task_at_simple'
                    ]
                ]
            )
            ->getMock()
        );

        $this->sm->setService(
            'Entity\TaskAllocationRule',
            m::mock()
            ->shouldReceive('findByQuery')
            ->with(
                [
                    'category' => 1234,
                    'isMlh' => 'NULL',
                    'trafficArea' => 'NULL'
                ]
            )
            ->andReturn(
                [
                    'Count' => 2
                ]
            )
            ->getMock()
        );

        $this->sm->setService(
            'Entity\SystemParameter',
            m::mock()
            ->shouldReceive('getValue')
            ->with('task.default_team')
            ->andReturn(4321)
            ->shouldReceive('getValue')
            ->with('task.default_user')
            ->andReturn(5678)
            ->getMock()
        );

        $assignment = $this->sut->getAssignment(['category' => 1234]);

        $this->assertEquals(
            [
                'assignedToTeam' => 4321,
                'assignedToUser' => 5678
            ],
            $assignment
        );
    }
}
