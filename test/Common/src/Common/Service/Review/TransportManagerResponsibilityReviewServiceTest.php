<?php

/**
 * Transport Manager Responsibility Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\TransportManagerResponsibilityReviewService;
use CommonTest\Bootstrap;
use Common\Service\Data\CategoryDataService;

/**
 * Transport Manager Responsibility Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TransportManagerResponsibilityReviewServiceTest extends MockeryTestCase
{
    protected $sut;

    protected $sm;

    public function setUp()
    {
        $this->sut = new TransportManagerResponsibilityReviewService();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @dataProvider provider
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

    public function provider()
    {
        return [
            [
                [
                    'tmType' => [
                        'description' => 'Internal'
                    ],
                    'isOwner' => 'Y',
                    'hoursMon' => 2,
                    'hoursTue' => 3,
                    'hoursWed' => 4,
                    'hoursThu' => 5,
                    'hoursFri' => 6,
                    'hoursSat' => 7,
                    'hoursSun' => 8,
                    'additionalInformation' => 'Foo bar cake',
                    'transportManager' => [
                        'documents' => []
                    ],
                    'otherLicences' => [

                    ],
                    'operatingCentres' => [
                        [
                            'address' => [
                                'addressLine1' => 'Foo'
                            ]
                        ],
                        [
                            'address' => [
                                'addressLine1' => 'Bar'
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
                                                'label' => 'tm-review-responsibility-operating-centres',
                                                'noEscape' => true,
                                                'value' => 'Foo<br>Bar'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tmType',
                                                'value' => 'Internal'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-isOwner',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-hours-per-week-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-mon',
                                                'value' => '2 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tue',
                                                'value' => '3 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-wed',
                                                'value' => '4 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-thu',
                                                'value' => '5 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-fri',
                                                'value' => '6 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sat',
                                                'value' => '7 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sun',
                                                'value' => '8 hours-translated'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-additional-info-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-additional-info',
                                                'value' => 'Foo bar cake'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-additional-info-files',
                                                'noEscape' => true,
                                                'value' => 'tm-review-responsibility-no-files-translated'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'tm-review-responsibility-other-licences',
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-responsibility-other-licences-none-added-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'tmType' => [
                        'description' => 'Internal'
                    ],
                    'isOwner' => 'Y',
                    'hoursMon' => 2,
                    'hoursTue' => 3,
                    'hoursWed' => 4,
                    'hoursThu' => 5,
                    'hoursFri' => 6,
                    'hoursSat' => 7,
                    'hoursSun' => 8,
                    'additionalInformation' => 'Foo bar cake',
                    'transportManager' => [
                        'documents' => [
                            [
                                'filename' => 'File1',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
                                ]
                            ],
                            [
                                'filename' => 'File2',
                                'category' => [
                                    'id' => 'FOOCAT'
                                ],
                                'subCategory' => [
                                    'id' => 'BARCAT'
                                ]
                            ],
                            [
                                'filename' => 'File3',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
                                ]
                            ]
                        ]
                    ],
                    'otherLicences' => [

                    ],
                    'operatingCentres' => [
                        [
                            'address' => [
                                'addressLine1' => 'Foo'
                            ]
                        ],
                        [
                            'address' => [
                                'addressLine1' => 'Bar'
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
                                                'label' => 'tm-review-responsibility-operating-centres',
                                                'noEscape' => true,
                                                'value' => 'Foo<br>Bar'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tmType',
                                                'value' => 'Internal'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-isOwner',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-hours-per-week-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-mon',
                                                'value' => '2 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tue',
                                                'value' => '3 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-wed',
                                                'value' => '4 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-thu',
                                                'value' => '5 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-fri',
                                                'value' => '6 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sat',
                                                'value' => '7 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sun',
                                                'value' => '8 hours-translated'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-additional-info-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-additional-info',
                                                'value' => 'Foo bar cake'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-additional-info-files',
                                                'noEscape' => true,
                                                'value' => 'File1<br>File3'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'tm-review-responsibility-other-licences',
                            'mainItems' => [
                                [
                                    'freetext' => 'tm-review-responsibility-other-licences-none-added-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                [
                    'tmType' => [
                        'description' => 'Internal'
                    ],
                    'isOwner' => 'Y',
                    'hoursMon' => 2,
                    'hoursTue' => 3,
                    'hoursWed' => 4,
                    'hoursThu' => 5,
                    'hoursFri' => 6,
                    'hoursSat' => 7,
                    'hoursSun' => 8,
                    'additionalInformation' => 'Foo bar cake',
                    'transportManager' => [
                        'documents' => [
                            [
                                'filename' => 'File1',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
                                ]
                            ],
                            [
                                'filename' => 'File2',
                                'category' => [
                                    'id' => 'FOOCAT'
                                ],
                                'subCategory' => [
                                    'id' => 'BARCAT'
                                ]
                            ],
                            [
                                'filename' => 'File3',
                                'category' => [
                                    'id' => CategoryDataService::CATEGORY_TRANSPORT_MANAGER
                                ],
                                'subCategory' => [
                                    'id' => CategoryDataService::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_TM1_ASSISTED_DIGITAL
                                ]
                            ]
                        ]
                    ],
                    'otherLicences' => [
                        [
                            'licNo' => 'AB12345678',
                            'role' => [
                                'description' => 'Transport manager'
                            ],
                            'operatingCentres' => 10,
                            'totalAuthVehicles' => 20,
                            'hoursPerWeek' => 30
                        ],
                        [
                            'licNo' => 'BA98765421',
                            'role' => [
                                'description' => 'Transport manager'
                            ],
                            'operatingCentres' => 20,
                            'totalAuthVehicles' => 10,
                            'hoursPerWeek' => 15
                        ]
                    ],
                    'operatingCentres' => [
                        [
                            'address' => [
                                'addressLine1' => 'Foo'
                            ]
                        ],
                        [
                            'address' => [
                                'addressLine1' => 'Bar'
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
                                                'label' => 'tm-review-responsibility-operating-centres',
                                                'noEscape' => true,
                                                'value' => 'Foo<br>Bar'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tmType',
                                                'value' => 'Internal'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-isOwner',
                                                'value' => 'Yes'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-hours-per-week-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-mon',
                                                'value' => '2 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-tue',
                                                'value' => '3 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-wed',
                                                'value' => '4 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-thu',
                                                'value' => '5 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-fri',
                                                'value' => '6 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sat',
                                                'value' => '7 hours-translated'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-sun',
                                                'value' => '8 hours-translated'
                                            ]
                                        ],
                                    ]
                                ],
                                [
                                    'header' => 'tm-review-responsibility-additional-info-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-additional-info',
                                                'value' => 'Foo bar cake'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-additional-info-files',
                                                'noEscape' => true,
                                                'value' => 'File1<br>File3'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'title' => 'tm-review-responsibility-other-licences',
                            'mainItems' => [
                                [
                                    'header' => 'AB12345678',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-role',
                                                'value' => 'Transport manager'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-operating-centres',
                                                'value' => 10
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-vehicles',
                                                'value' => 20
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-hours-per-week',
                                                'value' => 30
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'BA98765421',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-role',
                                                'value' => 'Transport manager'
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-operating-centres',
                                                'value' => 20
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-vehicles',
                                                'value' => 10
                                            ],
                                            [
                                                'label' => 'tm-review-responsibility-other-licences-hours-per-week',
                                                'value' => 15
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
