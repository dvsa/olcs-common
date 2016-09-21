<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\DashboardApplicationLink;
use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Common\Service\Table\Formatter\DashboardApplicationLink
 */
class DashboardApplicationLinkTest extends MockeryTestCase
{
    /**
     * Test format
     *
     * @dataProvider provider
     */
    public function testFormat($data, $column, $expectedRoute, $expectedParams, $expected)
    {
        $mockUrl = m::mock()
            ->shouldReceive('fromRoute')
            ->with($expectedRoute, $expectedParams)
            ->andReturn($expectedRoute . '/' . $expectedParams['application'])
            ->getMock();

        $mockTranslator = m::mock()
            ->shouldReceive('translate')
            ->once()
            ->andReturnUsing(
                function ($desc) {
                    return '_TRNLTD_' . $desc;
                }
            )
            ->getMock();

        $sm = Bootstrap::getServiceManager()
            ->setService('Helper\Url', $mockUrl)
            ->setService('translator', $mockTranslator);

        $this->assertEquals($expected, DashboardApplicationLink::format($data, $column, $sm));
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
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
                        'description' => 'Not sumbitted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application',
                'expectedParams' => [
                    'application' => 2
                ],
                'expect' => '<b><a href="lva-application/2">OB123/2</a></b> ' .
                    '<span class="status grey">_TRNLTD_Not sumbitted</span>'
            ],
            'Not sumbitted variation' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
                        'description' => 'Not sumbitted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'variation'
                ],
                'expectedRoute' => 'lva-variation',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-variation/2">OB123/2</a></b> ' .
                    '<span class="status grey">_TRNLTD_Not sumbitted</span>'
            ],
            'Under consideration' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_UNDER_CONSIDERATION,
                        'description' => 'Under consideration'
                    ],
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">2</a></b> ' .
                    '<span class="status orange">_TRNLTD_Under consideration</span>'
            ],
            'Valid' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_VALID,
                        'description' => 'Valid'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status green">_TRNLTD_Valid</span>'
            ],
            'Granted' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_GRANTED,
                        'description' => 'Granted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status green">_TRNLTD_Granted</span>'
            ],
            'Withdrawn' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_WITHDRAWN,
                        'description' => 'Withdrawn'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status red">_TRNLTD_Withdrawn</span>'
            ],
            'Refused' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_REFUSED,
                        'description' => 'Refused'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status red">_TRNLTD_Refused</span>'
            ],
            'Not taken up' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_TAKEN_UP,
                        'description' => 'Not taken up'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status red">_TRNLTD_Not taken up</span>'
            ],
            'Cancelled' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_CANCELLED,
                        'description' => 'Cancelled'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status grey">_TRNLTD_Cancelled</span>'
            ],
            'Unknown' => [
                'data' => [
                    'status' => [
                        'id' => 'unknown',
                        'description' => 'Unknown'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<b><a href="lva-application/submission-summary/2">OB123/2</a></b> ' .
                    '<span class="status grey">_TRNLTD_Unknown</span>'
            ],
        ];
    }
}
