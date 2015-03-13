<?php

/**
 * Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\VehiclesPsvReviewService;
use Common\Service\Entity\VehicleEntityService;

/**
 * Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new VehiclesPsvReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($mainItems, $data, $expected)
    {
        $mockTranslator = m::mock();
        $this->sm->setService('Helper\Translation', $mockTranslator);

        $mockTranslator->shouldReceive('translate')
            ->andReturnUsing(
                function ($string) {
                    return $string . '-translated';
                }
            );

        $this->assertEquals($expected, $this->sut->getConfigFromData($data, $mainItems));
    }

    public function providerGetConfigFromData()
    {
        return [
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'hasEnteredReg' => 'N'
                ],
                [
                    ['foo' => 'bar']
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
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
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-small-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar (application-review-vehicles-is-novelty-translated)'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ]
                        ]
                    ],
                    [
                        'header' => 'application-review-vehicles-psv-medium-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'ME10QWE'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'ME11QWE'
                                ]
                            ]
                        ]
                    ],
                    [
                        'header' => 'application-review-vehicles-psv-large-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG10QWE'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG11QWE'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
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
                        ]
                    ]
                ],
                [
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-small-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM10QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar (application-review-vehicles-is-novelty-translated)'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'SM11QWE'
                                ],
                                [
                                    'label' => 'application-review-vehicles-make',
                                    'value' => 'Foo Bar'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    ['foo' => 'bar']
                ],
                [
                    'hasEnteredReg' => 'Y',
                    'licenceVehicles' => [
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
                    ['foo' => 'bar'],
                    [
                        'header' => 'application-review-vehicles-psv-large-title',
                        'multiItems' => [
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG10QWE'
                                ]
                            ],
                            [
                                [
                                    'label' => 'application-review-vehicles-vrm',
                                    'value' => 'LG11QWE'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
