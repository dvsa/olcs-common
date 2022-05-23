<?php

/**
 * Licence number and status test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Table\Formatter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Table\Formatter\LicenceNumberAndStatus as sut;
use CommonTest\Bootstrap;
use Common\RefData;

/**
 * Licence number and status test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceNumberAndStatusTest extends MockeryTestCase
{
    /**
     * Test format
     *
     * @dataProvider provider
     * @param array $data
     * @param string $expected
     */
    public function testFormat($data, $expected)
    {

        $mockUrl = m::mock();
        $mockTranslator = m::mock();
        $mockTranslator->shouldReceive('translate')->andReturnUsing(
            function ($message) {
                return 'TRANSLATED_'. $message;
            }
        );

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Helper\Url', $mockUrl);
        $sm->setService('translator', $mockTranslator);

        $mockUrl->shouldReceive('fromRoute')
            ->with('lva-licence', ['licence' => 2])
            ->andReturn('lva-licence/2');

        $this->assertEquals($expected, sut::format($data, [], $sm));
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function provider()
    {
        return [
            'Valid' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_VALID,
                        'description' => 'Valid'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--green">Valid</span></a>',
            ],
            'Suspended' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SUSPENDED,
                        'description' => 'Suspended'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--orange">Suspended</span></a>',
            ],
            'Curtailed' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CURTAILED,
                        'description' => 'Curtailed'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--orange">Curtailed</span></a>',
            ],
            'Under consideration' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_UNDER_CONSIDERATION,
                        'description' => 'Under consideration'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--orange">Under consideration</span></a>',
            ],
            'Granted' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_GRANTED,
                        'description' => 'Granted'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--orange">Granted</span></a>',
            ],
            'Surrender under consideration' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION,
                        'description' => 'Surrender under consideration'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<div class="overview__link"><span>OB123</span> <span class="govuk-tag govuk-tag--green">Surrender under consideration</span></div>',
            ],
            'Surrendered' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SURRENDERED,
                        'description' => 'Surrendered'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Surrendered</span></a>',
            ],
            'Revoked' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_REVOKED,
                        'description' => 'Revoked'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Revoked</span></a>',
            ],
            'Terminated' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_TERMINATED,
                        'description' => 'Terminated'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Terminated</span></a>',
            ],
            'CNS' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
                        'description' => 'CNS'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">CNS</span></a>',
            ],
            'Withdrawn' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_WITHDRAWN,
                        'description' => 'Withdrawn'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Withdrawn</span></a>',
            ],
            'Refused' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_REFUSED,
                        'description' => 'Refused'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Refused</span></a>',
            ],
            'Not taken up' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_NOT_TAKEN_UP,
                        'description' => 'Not taken up'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">Not taken up</span></a>',
            ],
            'Cancelled' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CANCELLED,
                        'description' => 'Cancelled'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--grey">Cancelled</span></a>',
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
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--grey">Unknown</span></a>',
            ],
            'Expired' => [
                [
                    'status' => [
                        'id' => 'unknown',
                        'description' => 'Unknown'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2,
                    'isExpired' => true,
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">TRANSLATED_licence.status.expired</span></a>',
            ],
            'Expiring' => [
                [
                    'status' => [
                        'id' => 'unknown',
                        'description' => 'Unknown'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2,
                    'isExpiring' => true,
                ],
                '<a class="overview__link" href="lva-licence/2"><span>OB123</span> <span class="govuk-tag govuk-tag--red">TRANSLATED_licence.status.expiring</span></a>',
            ],
            'Expiring but Surrendered' =>[
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION,
                        'description' => 'Surrender under consideration'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2,
                    'isExpiring' => true,
                ],
                '<div class="overview__link"><span>OB123</span> <span class="govuk-tag govuk-tag--green">Surrender under consideration</span></div>',
            ],
        ];
    }
}
