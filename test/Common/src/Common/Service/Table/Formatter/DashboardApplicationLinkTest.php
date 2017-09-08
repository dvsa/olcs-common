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
                'expect' => '<a class="overview__link" href="lva-application/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status grey">_TRNLTD_Not sumbitted</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-variation/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status grey">_TRNLTD_Not sumbitted</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">2</span> '.
                    '<span class="overview__status orange">_TRNLTD_Under consideration</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status green">_TRNLTD_Valid</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status green">_TRNLTD_Granted</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status red">_TRNLTD_Withdrawn</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status red">_TRNLTD_Refused</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status red">_TRNLTD_Not taken up</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status grey">_TRNLTD_Cancelled</span></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2">'.
                    '<span class="overview__link--underline">OB123/2</span> '.
                    '<span class="overview__status grey">_TRNLTD_Unknown</span></a>',
            ],
        ];
    }
}
