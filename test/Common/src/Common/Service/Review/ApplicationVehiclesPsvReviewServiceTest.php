<?php

/**
 * Application Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationVehiclesPsvReviewService;
use Common\Service\Entity\VehicleEntityService;

/**
 * Application Vehicles Psv Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesPsvReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationVehiclesPsvReviewService();

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
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-hasEnteredReg',
                                                'value' => 'No'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
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
                            'mainItems' => [
                                [
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-vehicles-hasEnteredReg',
                                                'value' => 'Yes'
                                            ]
                                        ]
                                    ]
                                ],
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
                        ]
                    ]
                ]
            ]
        ];
    }
}
