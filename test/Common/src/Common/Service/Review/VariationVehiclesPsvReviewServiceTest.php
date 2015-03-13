<?php

/**
 * Variation Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\VariationVehiclesPsvReviewService;
use Common\Service\Entity\VehicleEntityService;

/**
 * Variation Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new VariationVehiclesPsvReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $mockVehiclesPsv = m::mock();
        $this->sm->setService('Review\VehiclesPsv', $mockVehiclesPsv);
        $mockVehiclesPsv->shouldReceive('getConfigFromData')
            ->with($data, [])
            ->andReturn('MAINITEMS');

        $this->assertEquals($expected, $this->sut->getConfigFromData($data));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    'hasEnteredReg' => 'N'
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => 'MAINITEMS'
                        ]
                    ]
                ]
            ],
            [
                [
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                                ],
                                'vrm' => 'SM10QWE',
                                'makeModel' => 'Foo Bar',
                                'isNovelty' => 'Y'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                                ],
                                'vrm' => 'SM11QWE',
                                'makeModel' => 'Foo Bar',
                                'isNovelty' => 'N'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                                ],
                                'vrm' => 'ME10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                                ],
                                'vrm' => 'ME11QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_LARGE
                                ],
                                'vrm' => 'LG10QWE'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'psvType' => [
                                    'id' => VehicleEntityService::PSV_TYPE_LARGE
                                ],
                                'vrm' => 'LG11QWE'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => 'MAINITEMS'
                        ]
                    ]
                ]
            ]
        ];
    }
}
