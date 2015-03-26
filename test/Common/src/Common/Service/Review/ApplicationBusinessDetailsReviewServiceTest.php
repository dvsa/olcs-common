<?php

/**
 * Application Business Details Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationBusinessDetailsReviewService;
use Common\Service\Entity\OrganisationEntityService;

/**
 * Application Business Details Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBusinessDetailsReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationBusinessDetailsReviewService();

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
            'Sole Trader (Without trading names)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_SOLE_TRADER
                            ],
                            'tradingNames' => [],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
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
                                        null,
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'review-none-added-translated'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Sole Trader (With trading names)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_SOLE_TRADER
                            ],
                            'tradingNames' => [
                                [
                                    'name' => 'My foo'
                                ],
                                [
                                    'name' => 'My bar'
                                ]
                            ],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
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
                                        null,
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'My foo'
                                            ],
                                            [
                                                // Only the first TN has a label
                                                'label' => '',
                                                'value' => 'My bar'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Partnership (Without trading names)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_PARTNERSHIP
                            ],
                            'name' => 'Our company',
                            'tradingNames' => [],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-partnership-name',
                                                'value' => 'Our company'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'review-none-added-translated'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Partnership (With trading names)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_PARTNERSHIP
                            ],
                            'name' => 'Our company',
                            'tradingNames' => [
                                [
                                    'name' => 'Our foo'
                                ],
                                [
                                    'name' => 'Our bar'
                                ]
                            ],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-partnership-name',
                                                'value' => 'Our company'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'Our foo'
                                            ],
                                            [
                                                // Only the first TN has a label
                                                'label' => '',
                                                'value' => 'Our bar'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Registered Company (No trading names or subsidiaries)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY
                            ],
                            'companyOrLlpNo' => '12345678',
                            'name' => 'Foo Ltd',
                            'tradingNames' => [],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
                                ]
                            ],
                            'contactDetails' => [
                                'address' => [
                                    'addressLine1' => '123',
                                    'addressLine2' => 'Foo lane',
                                    'town' => 'Bartown',
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-company-no',
                                                'value' => '12345678'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-company-name',
                                                'value' => 'Foo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'review-none-added-translated'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-registered-address',
                                                'value' => '123, Foo lane, Bartown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-business-details-subsidiary-company-header',
                                    'freetext' => 'review-none-added-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Registered Company (With trading names and subsidiaries)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [
                            [
                                'name' => 'Foosub Ltd',
                                'companyNo' => '23456789'
                            ],
                            [
                                'name' => 'Barsub Ltd',
                                'companyNo' => '34567891'
                            ]
                        ],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_REGISTERED_COMPANY
                            ],
                            'companyOrLlpNo' => '12345678',
                            'name' => 'Foo Ltd',
                            'tradingNames' => [
                                [
                                    'name' => 'Foobar Ltd'
                                ],
                                [
                                    'name' => 'Barfoo Ltd'
                                ]
                            ],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
                                ]
                            ],
                            'contactDetails' => [
                                'address' => [
                                    'addressLine1' => '123',
                                    'addressLine2' => 'Foo lane',
                                    'town' => 'Bartown',
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-company-no',
                                                'value' => '12345678'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-company-name',
                                                'value' => 'Foo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'Foobar Ltd'
                                            ],
                                            [
                                                // Only the first TN has a label
                                                'label' => '',
                                                'value' => 'Barfoo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-registered-address',
                                                'value' => '123, Foo lane, Bartown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-business-details-subsidiary-company-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label'
                                                    => 'application-review-business-details-subsidiary-company-name',
                                                'value' => 'Foosub Ltd'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-subsidiary-company-no',
                                                'value' => '23456789'
                                            ]
                                        ],
                                        [
                                            [
                                                'label'
                                                    => 'application-review-business-details-subsidiary-company-name',
                                                'value' => 'Barsub Ltd'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-subsidiary-company-no',
                                                'value' => '34567891'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // Should be identical to Ltd
            'LLP (No trading names or subsidiaries)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_LLP
                            ],
                            'companyOrLlpNo' => '12345678',
                            'name' => 'Foo Ltd',
                            'tradingNames' => [],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
                                ]
                            ],
                            'contactDetails' => [
                                'address' => [
                                    'addressLine1' => '123',
                                    'addressLine2' => 'Foo lane',
                                    'town' => 'Bartown',
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-company-no',
                                                'value' => '12345678'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-company-name',
                                                'value' => 'Foo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'review-none-added-translated'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-registered-address',
                                                'value' => '123, Foo lane, Bartown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-business-details-subsidiary-company-header',
                                    'freetext' => 'review-none-added-translated'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'LLP (With trading names and subsidiaries)' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [
                            [
                                'name' => 'Foosub Ltd',
                                'companyNo' => '23456789'
                            ],
                            [
                                'name' => 'Barsub Ltd',
                                'companyNo' => '34567891'
                            ]
                        ],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_LLP
                            ],
                            'companyOrLlpNo' => '12345678',
                            'name' => 'Foo Ltd',
                            'tradingNames' => [
                                [
                                    'name' => 'Foobar Ltd'
                                ],
                                [
                                    'name' => 'Barfoo Ltd'
                                ]
                            ],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
                                ]
                            ],
                            'contactDetails' => [
                                'address' => [
                                    'addressLine1' => '123',
                                    'addressLine2' => 'Foo lane',
                                    'town' => 'Bartown',
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-company-no',
                                                'value' => '12345678'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-company-name',
                                                'value' => 'Foo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-trading-names',
                                                'value' => 'Foobar Ltd'
                                            ],
                                            [
                                                // Only the first TN has a label
                                                'label' => '',
                                                'value' => 'Barfoo Ltd'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ],
                                        [
                                            [
                                                'label' => 'application-review-business-details-registered-address',
                                                'value' => '123, Foo lane, Bartown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-business-details-subsidiary-company-header',
                                    'multiItems' => [
                                        [
                                            [
                                                'label'
                                                    => 'application-review-business-details-subsidiary-company-name',
                                                'value' => 'Foosub Ltd'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-subsidiary-company-no',
                                                'value' => '23456789'
                                            ]
                                        ],
                                        [
                                            [
                                                'label'
                                                    => 'application-review-business-details-subsidiary-company-name',
                                                'value' => 'Barsub Ltd'
                                            ],
                                            [
                                                'label' => 'application-review-business-details-subsidiary-company-no',
                                                'value' => '34567891'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Other' => [
                [
                    'licence' => [
                        'companySubsidiaries' => [],
                        'organisation' => [
                            'type' => [
                                'id' => OrganisationEntityService::ORG_TYPE_OTHER
                            ],
                            'name' => 'My other business',
                            'tradingNames' => [],
                            'natureOfBusinesses' => [
                                [
                                    'description' => 'Foobar'
                                ],
                                [
                                    'description' => 'Barfoo'
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
                                        [
                                            [
                                                'label' => 'application-review-business-details-organisation-name',
                                                'value' => 'My other business'
                                            ]
                                        ],
                                        null,
                                        [
                                            [
                                                'label' => 'application-review-business-details-nature-of-business',
                                                'value' => 'Foobar'
                                            ],
                                            [
                                                // Only the first NoB has a label
                                                'label' => '',
                                                'value' => 'Barfoo'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
