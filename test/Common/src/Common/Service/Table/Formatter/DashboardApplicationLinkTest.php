<?php

/**
 * Dashboard Application Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DashboardApplicationLink as sut;
use CommonTest\Bootstrap;

/**
 * Dashboard Application Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DashboardApplicationLinkTest extends MockeryTestCase
{
    /**
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expectedRoute, $expectedParams, $expected)
    {

        $mockUrl = m::mock();

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Helper\Url', $mockUrl);

        $mockUrl->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedParams)
            ->andReturn($expectedRoute . '/' . $expectedParams['application']);

        $this->assertEquals($expected, sut::format($data, $column, $sm));
    }

    public function provider()
    {
        return [
            [
                [
                    'status' => 'apsts_not_submitted',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application',
                ['application' => 2],
                '<b><a href="lva-application/2">2</a></b>'
            ],
            [
                [
                    'status' => 'apsts_not_submitted',
                    'id' => 2
                ],
                [
                    'lva' => 'variation'
                ],
                'lva-variation',
                ['application' => 2],
                '<b><a href="lva-variation/2">2</a></b>'
            ],
            [
                [
                    'status' => 'apsts_consideration',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">2</a></b>'
            ],
            [
                [
                    'status' => 'apsts_consideration',
                    'id' => 2
                ],
                [
                    'lva' => 'variation'
                ],
                'lva-variation/submission-summary',
                ['application' => 2],
                '<b><a href="lva-variation/submission-summary/2">2</a></b>'
            ]
        ];
    }
}
