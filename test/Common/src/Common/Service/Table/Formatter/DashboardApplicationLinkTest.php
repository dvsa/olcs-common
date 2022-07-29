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

        $sm = Bootstrap::getServiceManager()
            ->setService('Helper\Url', $mockUrl);

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
                'expect' => '<a class="govuk-link" href="lva-application/2">OB123/2</a>',
            ],
            'Not sumbitted variation' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'variation'
                ],
                'expectedRoute' => 'lva-variation',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-variation/2">OB123/2</a>',
            ],
            'Under consideration' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_UNDER_CONSIDERATION,
                    ],
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">2</a>',
            ],
            'Valid' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_VALID,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Granted' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_GRANTED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Withdrawn' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_WITHDRAWN,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Refused' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_REFUSED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Not taken up' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_NOT_TAKEN_UP,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Cancelled' => [
                'data' => [
                    'status' => [
                        'id' => RefData::APPLICATION_STATUS_CANCELLED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
            'Unknown' => [
                'data' => [
                    'status' => [
                        'id' => 'unknown',
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'column' => [
                    'lva' => 'application'
                ],
                'expectedRoute' => 'lva-application/submission-summary',
                'expectedParams' => ['application' => 2],
                'expect' => '<a class="govuk-link" href="lva-application/submission-summary/2">OB123/2</a>',
            ],
        ];
    }
}
