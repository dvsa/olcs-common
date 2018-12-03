<?php

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TaskDescription;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Task description formatter tests
 */
class TaskDescriptionTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestFormat
     */
    public function testFormat($matchedRouteName, $params, $expected)
    {
        $data = [
            'id' => 100,
            'description' => 'DESC',
        ];
        $query = ['q1' => 1];

        $sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $sm->shouldReceive('get->match->getMatchedRouteName')
            ->withNoArgs()
            ->andReturn($matchedRouteName);
        $sm->shouldReceive('get->match->getParams')
            ->withNoArgs()
            ->andReturn($params);
        $sm->shouldReceive('get->fromRoute')
            ->with(
                'task_action',
                $expected,
                ['query' => $query]
            )
            ->andReturn('URL');
        $sm->shouldReceive('get->getQuery->toArray')
            ->withNoArgs()
            ->andReturn($query);

        $this->assertEquals('<a href="URL" class="js-modal-ajax">DESC</a>', TaskDescription::format($data, [], $sm));
    }

    public function dpTestFormat()
    {
        return [
            [
                'unmatched-route',
                [],
                [
                    'task' => 100,
                    'action' => 'edit'
                ],
            ],
            [
                'licence/processing/tasks',
                [
                    'licence' => 201,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'licence',
                    'typeId' => 201,
                ],
            ],
            [
                'lva-application/processing/tasks',
                [
                    'application' => 201,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'application',
                    'typeId' => 201,
                ],
            ],
            [
                'transport-manager/processing/tasks',
                [
                    'transportManager' => 201,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'tm',
                    'typeId' => 201,
                ],
            ],
            [
                'licence/bus-processing/tasks',
                [
                    'busRegId' => 201,
                    'licence' => 202,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'busreg',
                    'typeId' => 201,
                    'licence' => 202,
                ],
            ],
            [
                'licence/irhp-processing/tasks',
                [
                    'permitid' => 201,
                    'licence' => 202,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'ecmtpermitapplication',
                    'typeId' => 201,
                    'licence' => 202,
                ],
            ],
            [
                'case_processing_tasks',
                [
                    'case' => 201,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'case',
                    'typeId' => 201,
                ],
            ],
            [
                'operator/processing/tasks',
                [
                    'organisation' => 201,
                ],
                [
                    'task' => 100,
                    'action' => 'edit',
                    'type' => 'organisation',
                    'typeId' => 201,
                ],
            ],
        ];
    }
}
