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
            'Not submitted' => [
                [
                    'status' => [
                        'id' => RefData::LICENCE_STATUS_VALID,
                        'description' => 'Valid'
                    ],
                    'licNo' => 'OB123',
                    'id' => 2
                ],
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status green">Valid</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status orange">Suspended</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status orange">Curtailed</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status orange">Under consideration</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status orange">Granted</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Surrendered</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Revoked</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Terminated</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">CNS</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Withdrawn</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Refused</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status red">Not taken up</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status grey">Cancelled</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> <span class="status grey">Unknown</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> '.
                    '<span class="status red">TRANSLATED_licence.status.expired</span>'
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
                '<b><a href="lva-licence/2">OB123</a></b> '.
                    '<span class="status red">TRANSLATED_licence.status.expiring</span>'
            ],
        ];
    }
}
