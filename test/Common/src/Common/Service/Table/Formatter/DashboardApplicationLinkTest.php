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
                'expect' => '<a class="overview__link" href="lva-application/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--grey">_TRNLTD_Not sumbitted</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-variation/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--grey">_TRNLTD_Not sumbitted</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>2</span> <strong class="govuk-tag govuk-tag--orange">_TRNLTD_Under consideration</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--green">_TRNLTD_Valid</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--green">_TRNLTD_Granted</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--red">_TRNLTD_Withdrawn</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--red">_TRNLTD_Refused</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--red">_TRNLTD_Not taken up</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--grey">_TRNLTD_Cancelled</strong></a>',
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
                'expect' => '<a class="overview__link" href="lva-application/submission-summary/2"><span>OB123/2</span> <strong class="govuk-tag govuk-tag--grey">_TRNLTD_Unknown</strong></a>',
            ],
        ];
    }
}
