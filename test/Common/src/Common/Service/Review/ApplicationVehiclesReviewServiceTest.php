<?php

/**
 * Application Vehicles Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationVehiclesReviewService;

/**
 * Application Vehicles Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApplicationVehiclesReviewService();
    }

    /**
     * @dataProvider providerGetConfigFromData
     */
    public function testGetConfigFromData($data, $expected)
    {
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
                                'vrm' => 'AB12QWE',
                                'platedWeight' => '1000'
                            ]
                        ],
                        [
                            'vehicle' => [
                                'vrm' => 'AB13QWE',
                                'platedWeight' => '10000'
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
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-vehicles-vrm',
                                                'value' => 'AB12QWE'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-weight',
                                                'value' => '1,000 Kg'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-vehicles-vrm',
                                                'value' => 'AB13QWE'
                                            ],
                                            [
                                                'label' => 'application-review-vehicles-weight',
                                                'value' => '10,000 Kg'
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
