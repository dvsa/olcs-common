<?php

/**
 * Dashboard Application Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\DashboardApplicationLink as sut;
use CommonTest\Bootstrap;
use Common\RefData;

/**
 * Dashboard Application Link Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DashboardApplicationLinkTest extends MockeryTestCase
{
    /**
     * Test format
     *
     * @dataProvider provider
     * @param array $data
     * @param array $column
     * @param srring $expectedRoute
     * @param array $expectedParams
     * @param string $expected
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

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'Not submitted' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
                        'description' => 'Not sumbitted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application',
                ['application' => 2],
                '<b><a href="lva-application/2">OB123/2</a></b> <span class="status grey">Not sumbitted</span>'
            ],
            'Not sumbitted variation' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
                        'description' => 'Not sumbitted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'variation'
                ],
                'lva-variation',
                ['application' => 2],
                '<b><a href="lva-variation/2">OB123/2</a></b> <span class="status grey">Not sumbitted</span>'
            ],
            'Under consideration' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        'description' => 'Under consideration'
                    ],
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">2</a></b> <span class="status orange">' .
                    'Under consideration</span>'
            ],
            'Valid' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_VALID,
                        'description' => 'Valid'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status green">' .
                    'Valid</span>'
            ],
            'Granted' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_GRANTED,
                        'description' => 'Granted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status green">' .
                'Granted</span>'
            ],
            'Withdrawn' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_WITHDRAWN,
                        'description' => 'Withdrawn'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status red">' .
                'Withdrawn</span>'
            ],
            'Refused' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_REFUSED,
                        'description' => 'Refused'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status red">' .
                'Refused</span>'
            ],
            'Not taken up' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_TAKEN_UP,
                        'description' => 'Not taken up'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status red">' .
                'Not taken up</span>'
            ],
            'Cancelled' => [
                [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_CANCELLED,
                        'description' => 'Cancelled'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status grey">' .
                'Cancelled</span>'
            ],
            'Unknown' => [
                [
                    'status' => [
                        'id' => 'unknown',
                        'description' => 'Unknown'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                [
                    'lva' => 'application'
                ],
                'lva-application/submission-summary',
                ['application' => 2],
                '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> <span class="status grey">' .
                'Unknown</span>'
            ],
        ];
    }
}
