<?php

/**
 * InspectionRequestId Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Common\Service\Table\Formatter\InspectionRequestId;

/**
 * InspectionRequestId Formatter Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestIdTest extends MockeryTestCase
{
    /**
     * Test formatter
     * 
     * @group inspectionRequestIdFormatter
     * @dataProvider formatProvider
     */
    public function testFormat($routeName, $urlParams, $data, $url, $expected)
    {
        $mockRequest = m::mock();

        $sm = m::mock()
            ->shouldReceive('get')
            ->with('request')
            ->andReturn($mockRequest)
            ->once()
            ->shouldReceive('get')
            ->with('router')
            ->andReturn(
                m::mock()
                ->shouldReceive('match')
                ->with($mockRequest)
                ->andReturn(
                    m::mock()
                    ->shouldReceive('getMatchedRouteName')
                    ->andReturn($routeName)
                    ->getMock()
                )
                ->getMock()
            )
            ->once()
            ->getMock();

        if ($url) {
            $mockUrlHelper = m::mock()
                ->shouldReceive('fromRoute')
                ->with($routeName, $urlParams)
                ->andReturn($url)
                ->getMock();
        } else {
            $mockUrlHelper = m::mock();
        }

        $sm->shouldReceive('get')
            ->with('Helper\Url')
            ->andReturn($mockUrlHelper)
            ->getMock();

        $expected = '<a href="'
            . $url
            . '" class=js-modal-ajax>'
            . $data['id']
            . '</a>';

        $this->assertEquals($expected, InspectionRequestId::format($data, [], $sm));
    }

    public function formatProvider()
    {
        return [
            [
                'licence/processing/inspection-request',
                [
                    'action' => 'edit',
                    'licence' => 2,
                    'id' => 1,
                ],
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => ['id' => 3]
                ],
                'url1',
                '<a href="url1" class=js-modal-ajax>1</a>'
            ],
            [
                'lva-application/processing/inspection-request',
                [
                    'action' => 'edit',
                    'application' => 3,
                    'id' => 1,
                ],
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => ['id' => 3]
                ],
                'url2',
                '<a href="url2" class=js-modal-ajax>1</a>'
            ],
            [
                'lva-variation/processing/inspection-request',
                [
                    'action' => 'edit',
                    'application' => 3,
                    'id' => 1,
                ],
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => ['id' => 3]
                ],
                'url3',
                '<a href="url3" class=js-modal-ajax>1</a>'
            ],
            [
                '',
                [],
                [
                    'id' => 1,
                    'licence' => ['id' => 2],
                    'application' => ['id' => 3]
                ],
                '',
                '<a href="" class=js-modal-ajax>1</a>'
            ]
        ];
    }
}
