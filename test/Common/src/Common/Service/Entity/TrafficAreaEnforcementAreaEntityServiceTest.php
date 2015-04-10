<?php

/**
 * Traffic Area Enforcement Area Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TrafficAreaEnforcementAreaEntityService;
use Mockery as m;

/**
 * Traffic Area Enforcement Area Entity Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class TrafficAreaEnforcementAreaEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TrafficAreaEnforcementAreaEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetValueOptions()
    {
        $trafficArea = 'B';

        $query = [
            'trafficArea' => $trafficArea,
        ];

        $response = [
            'Count' => 3,
            'Results' => [
                [
                    'id' => 21,
                    'version' => 1,
                    'enforcementArea' => [
                        'id' => 'V029',
                        'emailAddress' => 'AABY.SCANTLIN@GARDAL.COM',
                        'name' => 'Nottingham',
                        'version' => 1,
                    ]
                ],
                [
                    'id' => 29,
                    'version' => 1,
                    'enforcementArea' => [
                        'id' => 'V042',
                        'emailAddress' => 'AABY.POLLINGER@PENNIE.COM',
                        'name' => 'Beverley',
                        'version' => 1,
                    ],
                ],
                [
                    'id' => 30,
                    'version' => 1,
                    'enforcementArea' => [
                        'id' => 'V045',
                        'emailAddress' => 'AABY.POLLINGER@PENNIE.COM',
                        'name' => 'Grimsby',
                        'version' => 1,
                    ],
                ]
            ],
        ];

        $this->expectOneRestCall('TrafficAreaEnforcementArea', 'GET', $query)
            ->will($this->returnValue($response));

        $expected = [
            'V042' => 'Beverley',
            'V045' => 'Grimsby',
            'V029' => 'Nottingham',
        ];

        $this->assertSame($expected, $this->sut->getValueOptions($trafficArea));
    }
}
