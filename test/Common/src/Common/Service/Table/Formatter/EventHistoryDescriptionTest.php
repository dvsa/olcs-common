<?php

/**
 * Event history description formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Common\Service\Table\Formatter\EventHistoryDescription;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;

/**
 * Event history description formatter test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class EventHistoryDescriptionTest extends MockeryTestCase
{

    /**
     * Test the format method
     *
     * @dataProvider provider
     */
    public function testFormat(
        $data,
        $expectedRouteName,
        $expectedUrlParams,
        $expectedUrl,
        $expectedOutput
    ) {
        $mockUrlHelper = m::mock()
            ->shouldReceive('fromRoute')
            ->with($expectedRouteName, $expectedUrlParams, [], true)
            ->andReturn($expectedUrl)
            ->getMock();

        $mockRequest = m::mock();

        $mockRouter = m::mock()
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn(
                m::mock()
                    ->shouldReceive('getMatchedRouteName')
                    ->once()
                    ->andReturn($expectedRouteName)
                    ->getMock()
            )
            ->once()
            ->getMock();

        $sm = m::mock()->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper)
            ->once()
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter)
            ->once()
            ->shouldReceive('get')
            ->with('request')
            ->andReturn($mockRequest)
            ->once()
            ->getMock();

        $this->assertEquals($expectedOutput, EventHistoryDescription::format($data, [], $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'application event history' => [
                [
                    'application' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'lva-application/processing/event-history',
                [
                    'action' => 'edit',
                    'application' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'variation event history' => [
                [
                    'application' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => ['description' => 'foo']
                ],
                'lva-application/processing/event-history',
                [
                    'action' => 'edit',
                    'application' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'licence event history' => [
                [
                    'licence' => ['id' => 2],
                    'id' => 1,
                ],
                'licence/processing/event-history',
                [
                    'action' => 'edit',
                    'licence' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar"></a>'
            ],
            'busreg event history' => [
                [
                    'busReg' => 2,
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'licence/bus-processing/event-history',
                [
                    'action' => 'edit',
                    'busRegId' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'transport manager event history' => [
                [
                    'transportManager' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'transport-manager/processing/event-history',
                [
                    'action' => 'edit',
                    'transportManager' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'operator event history' => [
                [
                    'organisation' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'operator/processing/history',
                [
                    'action' => 'edit',
                    'organisation' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'case event history' => [
                [
                    'case' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'processing_history',
                [
                    'action' => 'edit',
                    'case' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
            'irhp application event history' => [
                [
                    'irhpApplication' => ['id' => 2],
                    'id' => 1,
                    'eventHistoryType' => [
                        'description' => 'foo'
                    ]
                ],
                'processing_history',
                [
                    'action' => 'edit',
                    'irhpApplication' => 2,
                    'id' => 1,
                ],
                'bar',
                '<a class="govuk-link js-modal-ajax" href="bar">foo</a>'
            ],
        ];
    }

    /**
     * Test format with exception
     */
    public function testFormatWithException()
    {
        $this->expectException(\Exception::class);
        $mockRequest = m::mock();

        $mockRouter = m::mock()
            ->shouldReceive('match')
            ->with($mockRequest)
            ->andReturn(
                m::mock()
                    ->shouldReceive('getMatchedRouteName')
                    ->once()
                    ->andReturn('foo')
                    ->getMock()
            )
            ->once()
            ->getMock();

        $sm = m::mock()->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn(m::mock())
            ->once()
            ->shouldReceive('get')
            ->with('router')
            ->andReturn($mockRouter)
            ->once()
            ->shouldReceive('get')
            ->with('request')
            ->andReturn($mockRequest)
            ->once()
            ->getMock();

        $this->assertEquals('foo', EventHistoryDescription::format([], [], $sm));
    }
}
