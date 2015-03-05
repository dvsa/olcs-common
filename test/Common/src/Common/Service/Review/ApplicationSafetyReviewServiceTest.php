<?php

/**
 * Application Safety Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\Review\ApplicationSafetyReviewService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Safety Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationSafetyReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationSafetyReviewService();
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
            'PSV' => [
                [
                    'safetyConfirmation' => 'Y',
                    'goodsOrPsv' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                    ],
                    'licence' => [
                        'safetyInsVehicles' => 1,
                        'safetyInsTrailers' => 0,
                        'safetyInsVaries' => 'Y',
                        'tachographIns' => [
                            'id' => 'tach_external'
                        ],
                        'tachographInsName' => 'Bob',
                        'workshops' => [
                            [
                                'isExternal' => 'Y',
                                'contactDetails' => [
                                    'fao' => 'Bob Smith',
                                    'address' => [
                                        'addressLine1' => '123',
                                        'addressLine2' => 'Foo street',
                                        'town' => 'Footown'
                                    ]
                                ]
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
                                        'safetyIns' => [
                                            [
                                                'label' => 'application-review-safety-safetyInsVehicles',
                                                'value' => '1 {Week}-translated'
                                            ]
                                        ],
                                        'safetyInsVaries' => [
                                            [
                                                'label' => 'application-review-safety-safetyInsVaries-psv',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-safety-tachographIns',
                                                'value' => 'tachograph_analyser.tach_external-translated'
                                            ],
                                            [
                                                'label' => 'application-review-safety-tachographInsName',
                                                'value' => 'Bob'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-safety-safetyConfirmation',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-safety-workshop-title',
                            'mainItems' => [
                                [
                                    'header' => '123, Footown',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-safety-workshop-isExternal',
                                                'value' => 'application-review-safety-workshop-isExternal-Y-translated'
                                            ],
                                            [
                                                'label' => 'application-review-safety-workshop-name',
                                                'value' => 'Bob Smith'
                                            ],
                                            [
                                                'label' => 'application-review-safety-workshop-address',
                                                'value' => '123, Foo street, Footown'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Goods' => [
                [
                    'safetyConfirmation' => 'Y',
                    'goodsOrPsv' => [
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    ],
                    'licence' => [
                        'safetyInsVehicles' => 2,
                        'safetyInsTrailers' => 0,
                        'safetyInsVaries' => 'Y',
                        'tachographIns' => [
                            'id' => 'tach_external'
                        ],
                        'tachographInsName' => 'Bob',
                        'workshops' => [
                            [
                                'isExternal' => 'Y',
                                'contactDetails' => [
                                    'fao' => 'Bob Smith',
                                    'address' => [
                                        'addressLine1' => '123',
                                        'addressLine2' => 'Foo street',
                                        'town' => 'Footown'
                                    ]
                                ]
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
                                        'safetyIns' => [
                                            [
                                                'label' => 'application-review-safety-safetyInsVehicles',
                                                'value' => '2 {Weeks}-translated'
                                            ],
                                            [
                                                'label' => 'application-review-safety-safetyInsTrailers',
                                                'value' => 'N/A-translated'
                                            ]
                                        ],
                                        'safetyInsVaries' => [
                                            [
                                                'label' => 'application-review-safety-safetyInsVaries',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-safety-tachographIns',
                                                'value' => 'tachograph_analyser.tach_external-translated'
                                            ],
                                            [
                                                'label' => 'application-review-safety-tachographInsName',
                                                'value' => 'Bob'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-safety-safetyConfirmation',
                                                'value' => 'Confirmed'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'application-review-safety-workshop-title',
                            'mainItems' => [
                                [
                                    'header' => '123, Footown',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-safety-workshop-isExternal',
                                                'value' => 'application-review-safety-workshop-isExternal-Y-translated'
                                            ],
                                            [
                                                'label' => 'application-review-safety-workshop-name',
                                                'value' => 'Bob Smith'
                                            ],
                                            [
                                                'label' => 'application-review-safety-workshop-address',
                                                'value' => '123, Foo street, Footown'
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
