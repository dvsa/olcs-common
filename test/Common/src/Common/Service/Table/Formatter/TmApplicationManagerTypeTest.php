<?php

/**
 * TmApplicationManagerType Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */

namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\TmApplicationManagerType;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * TmApplicationManagerType Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmApplicationManagerTypeTest extends MockeryTestCase
{
    /**
     * Test formatter
     * 
     * @group tmApplicationManagerType
     * @dataProvider formatProvider
     */
    public function testFormat($data, $message, $status, $expected)
    {
        $routeParams = [
            'id' => 1,
            'action' => 'edit-tm-application',
            'transportManager' => 1
        ];

        $mockTranslator = m::mock();

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('Application')
            ->andReturn(
                m::mock()
                ->shouldReceive('getMvcEvent')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getRouteMatch')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getParam')
                        ->with('transportManager')
                        ->andReturn(1)
                        ->getMock()
                    )
                    ->getMock()
                )
                ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(
                m::mock()
                ->shouldReceive('fromRoute')
                ->with(null, $routeParams)
                ->andReturn('url')
                ->getMock()
            )
            ->once()
            ->shouldReceive('get')
            ->with('translator')
            ->andReturn($mockTranslator)
            ->getMock();

        if ($data['action'] !== '') {
            $mockTranslator->shouldReceive('translate')
                ->with($message)
                ->andReturn($status)
                ->getMock();
        }

        $this->assertEquals($expected, TmApplicationManagerType::format($data, [], $sm));
    }

    public function formatProvider()
    {
        return [
            [
                [
                    'id' => 1,
                    'action' => 'A',
                    'tmType' => ['description' => 'desc1']
                ],
                'tm_application.table.status.new',
                'status new',
                '<a class="govuk-link" href="url">desc1 status new</a>'
            ],
            [
                [
                    'id' => 1,
                    'action' => 'U',
                    'tmType' => ['description' => 'desc2']
                ],
                'tm_application.table.status.updated',
                'status updated',
                '<a class="govuk-link" href="url">desc2 status updated</a>'
            ],
            [
                [
                    'id' => 1,
                    'action' => 'D',
                    'tmType' => ['description' => 'desc3']
                ],
                'tm_application.table.status.removed',
                'status removed',
                'desc3 status removed'
            ],
            [
                [
                    'id' => 1,
                    'action' => '',
                    'tmType' => ['description' => 'desc4']
                ],
                '',
                '',
                '<a class="govuk-link" href="url">desc4</a>'
            ]
        ];
    }
}
