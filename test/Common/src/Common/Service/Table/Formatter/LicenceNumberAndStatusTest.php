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
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Suspended' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SUSPENDED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Curtailed' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CURTAILED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Under consideration' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_UNDER_CONSIDERATION,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Granted' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_GRANTED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Surrender under consideration' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                'OB123',
            ],
            'Surrendered' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_SURRENDERED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Revoked' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_REVOKED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Terminated' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_TERMINATED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'CNS' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Withdrawn' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_WITHDRAWN,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Refused' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_REFUSED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Not taken up' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_NOT_TAKEN_UP,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Cancelled' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_CANCELLED,
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Unknown' => [
                [
                    'status' => [
                        'id' => 'unknown',
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
            ],
            'Expired' => [
                [
                    'status' => [
                        'id' => 'unknown',
                    ],
                    'licNo' => 'OB123',
                    'id' => 2,
                    'isExpired' => true,
                ],
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
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
                '<a class="govuk-link" href="lva-licence/2">OB123</a>',
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
                'OB123',
            ],
        ];
    }
}
