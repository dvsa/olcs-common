<?php

/**
 * Application Addresses Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Review;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Review\ApplicationAddressesReviewService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\PhoneContactEntityService;

/**
 * Application Addresses Review Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationAddressesReviewServiceTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sut = new ApplicationAddressesReviewService();

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
        $data = [];

        $licenceTypes = [
            LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL
        ];

        foreach ($licenceTypes as $licenceType) {
            $data[$licenceType] = [
                [
                    'licenceType' => [
                        'id' => $licenceType
                    ],
                    'licence' => [
                        'correspondenceCd' => [
                            'fao' => 'Bob Smith',
                            'emailAddress' => 'bob@smith.com',
                            'address' => [
                                'addressLine1' => '123',
                                'addressLine2' => 'Foo street',
                                'town' => 'Footown'
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => ['id' => PhoneContactEntityService::TYPE_BUSINESS],
                                    'phoneNumber' => '0123456789'
                                ],
                                [
                                    'phoneContactType' => ['id' => PhoneContactEntityService::TYPE_MOBILE],
                                    'phoneNumber' => '0765465465'
                                ]
                            ]
                        ],
                        'establishmentCd' => [
                            'address' => [
                                'addressLine1' => '321',
                                'addressLine2' => 'Foo street',
                                'town' => 'Footown'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'application-review-addresses-correspondence-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-addresses-fao',
                                                'value' => 'Bob Smith'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-address',
                                                'value' => '123, Foo street, Footown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-addresses-contact-details-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-addresses-correspondence-business',
                                                'value' => '0123456789'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-home',
                                                'value' => ''
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-mobile',
                                                'value' => '0765465465'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-fax',
                                                'value' => ''
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-email',
                                                'value' => 'bob@smith.com'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-addresses-establishment-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-addresses-establishment-address',
                                                'value' => '321, Foo street, Footown'
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

        $data['Restricted'] = [
                [
                    'licenceType' => [
                        'id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                    ],
                    'licence' => [
                        'correspondenceCd' => [
                            'fao' => 'Bob Smith',
                            'emailAddress' => 'bob@smith.com',
                            'address' => [
                                'addressLine1' => '123',
                                'addressLine2' => 'Foo street',
                                'town' => 'Footown'
                            ],
                            'phoneContacts' => [
                                [
                                    'phoneContactType' => ['id' => PhoneContactEntityService::TYPE_BUSINESS],
                                    'phoneNumber' => '0123456789'
                                ],
                                [
                                    'phoneContactType' => ['id' => PhoneContactEntityService::TYPE_MOBILE],
                                    'phoneNumber' => '0765465465'
                                ]
                            ]
                        ],
                        'establishmentCd' => [
                            'address' => [
                                'addressLine1' => '321',
                                'addressLine2' => 'Foo street',
                                'town' => 'Footown'
                            ]
                        ]
                    ]
                ],
                [
                    'subSections' => [
                        [
                            'mainItems' => [
                                [
                                    'header' => 'application-review-addresses-correspondence-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-addresses-fao',
                                                'value' => 'Bob Smith'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-address',
                                                'value' => '123, Foo street, Footown'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    'header' => 'application-review-addresses-contact-details-title',
                                    'multiItems' => [
                                        [
                                            [
                                                'label' => 'application-review-addresses-correspondence-business',
                                                'value' => '0123456789'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-home',
                                                'value' => ''
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-mobile',
                                                'value' => '0765465465'
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-fax',
                                                'value' => ''
                                            ],
                                            [
                                                'label' => 'application-review-addresses-correspondence-email',
                                                'value' => 'bob@smith.com'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ];

        return $data;
    }
}
