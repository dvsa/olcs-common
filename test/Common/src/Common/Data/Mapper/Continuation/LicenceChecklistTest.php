<?php

namespace CommonTest\Data\Mapper\Continuation;

use Common\Data\Mapper\Continuation\LicenceChecklist;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\RefData;

/**
 * LicenceChecklist Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class LicenceChecklistTest extends MockeryTestCase
{
    /**
     * @dataProvider operatingFromProvider
     */
    public function testMapFromResultToView($key, $code)
    {
        $in = [
            'licence' => [
                'trafficArea' => [
                    'id' => $code,
                    'description' => 'Foo'
                ],
                'goodsOrPsv' => [
                    'id' => RefData::LICENCE_CATEGORY_GOODS_VEHICLE,
                    'description' => 'Bar'
                ],
                'licenceType' => [
                    'description' => 'Cake'
                ],
                'organisation' => [
                    'type' => [
                        'description' => 'Limited Company',
                        'id' => RefData::ORG_TYPE_REGISTERED_COMPANY
                    ],
                    'name' => 'Foo Ltd',
                    'companyOrLlpNo' => '12345678',
                    'organisationPersons' => [
                        [
                            'person' => [
                                'title' => [
                                    'description' => 'Mr'
                                ],
                                'familyName' => 'Bar',
                                'forename' => 'Foo',
                                'birthDate' => '1980/01/02'
                            ]
                        ],
                        [
                            'person' => [
                                'title' => [
                                    'description' => 'Doctor'
                                ],
                                'familyName' => 'Cake',
                                'forename' => 'Buz',
                                'birthDate' => '1980/02/01'
                            ]
                        ]
                    ]
                ],
                'tradingNames' => [
                    [
                        'name' => 'aaa'
                    ],
                    [
                        'name' => 'bbb'
                    ]
                ],
                'licenceVehicles' => [
                    [
                        'vehicle' => [
                            'vrm' => 'VRM456',
                            'platedWeight' => 1000,
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM123',
                            'platedWeight' => 2000,
                        ]
                    ],
                ],
                'correspondenceCd' => [
                    'address' => [
                        'addressLine1' => 'Flat 1',
                        'addressLine2' => 'Foo house',
                        'town' => 'Leeds',
                        'postcode' => 'LS9 6NF'
                    ],
                    'phoneContacts' => [
                        [
                            'phoneContactType' => [
                                'id' => RefData::PHONE_TYPE_PRIMARY
                            ],
                            'phoneNumber' => '123'
                        ],
                        [
                            'phoneContactType' => [
                                'id' => RefData::PHONE_TYPE_SECONDARY
                            ],
                            'phoneNumber' => '456'
                        ],
                    ]
                ],
                'establishmentCd' => [
                    'address' => [
                        'addressLine1' => 'Flat 99',
                        'addressLine2' => 'Bar house',
                        'town' => 'London',
                        'postcode' => 'SW1A 2AA'
                    ],
                ],
                'operatingCentres' => [
                    [
                        'operatingCentre' => [
                            'address' => [
                                'addressLine1' => 'Foo',
                                'town' => 'Bar'
                            ]
                        ],
                        'noOfVehiclesRequired' => 1,
                        'noOfTrailersRequired' => 2,
                    ],
                    [
                        'operatingCentre' => [
                            'address' => [
                                'addressLine1' => 'Cake',
                                'town' => 'Baz'
                            ]
                        ],
                        'noOfVehiclesRequired' => 3,
                        'noOfTrailersRequired' => 4,
                    ],
                ]
            ],
            'id' => 999,
        ];
        $out = [
            'data' => [
                'typeOfLicence' => [
                    'operatingFrom' => $key . '_translated',
                    'goodsOrPsv' => 'Bar',
                    'licenceType' => 'Cake'
                ],
                'businessType' => [
                    'typeOfBusiness' => 'Limited Company',
                    'typeOfBusinessId' => RefData::ORG_TYPE_REGISTERED_COMPANY
                ],
                'businessDetails' => [
                    'companyName' => 'Foo Ltd',
                    'companyNumber' => '12345678',
                    'organisationLabel' => 'continuations.business-details.company-name_translated',
                    'tradingNames' => 'aaa, bbb',
                ],
                'people' => [
                    'persons' => [
                        [
                            'name' => 'Doctor Buz Cake',
                            'birthDate' => '01/02/1980'
                        ],
                        [
                            'name' => 'Mr Foo Bar',
                            'birthDate' => '02/01/1980'
                        ]
                    ],
                    'header' =>
                        'continuations.people-section-header.' . RefData::ORG_TYPE_REGISTERED_COMPANY . '_translated',
                    'displayPersonCount' => RefData::CONTINUATIONS_DISPLAY_PERSON_COUNT
                ],
                'vehicles' => [
                    'vehicles' => [
                        [
                            'vrm' => 'VRM123',
                            'weight' => '2000kg',
                        ],
                        [
                            'vrm' => 'VRM456',
                            'weight' => '1000kg',
                        ]
                    ],
                    'isGoods' => true,
                    'displayVehiclesCount' => RefData::CONTINUATIONS_DISPLAY_VEHICLES_COUNT,
                    'header' => 'continuations.vehicles-section-header_translated'
                ],
                'addresses' => [
                    'correspondenceAddress' => 'Flat 1, Foo house, Leeds, LS9 6NF',
                    'establishmentAddress' => 'Flat 99, Bar house, London, SW1A 2AA',
                    'primaryNumber' => '123',
                    'secondaryNumber' => '456',
                ],
                'operatingCentres' => [
                    'operatingCentres' => [
                        [
                            'name' => 'Cake, Baz',
                            'vehicles' => 3,
                            'trailers' => 4,
                        ],
                        [
                            'name' => 'Foo, Bar',
                            'vehicles' => 1,
                            'trailers' => 2,
                        ],
                    ],
                    'totalOperatingCentres' => 2,
                    'totalVehicles' => 4,
                    'totalTrailers' => 6,
                    'isGoods' => true,
                    'displayOperatingCentresCount' => RefData::CONTINUATIONS_DISPLAY_OPERATING_CENTRES_COUNT
                ],
                'continuationDetailId' => 999,
            ],

        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->assertEquals($out, LicenceChecklist::mapFromResultToView($in, $mockTranslator));
    }

    public function operatingFromProvider()
    {
        return [
            ['continuations.type-of-licence.ni', RefData::NORTHERN_IRELAND_TRAFFIC_AREA_CODE],
            ['continuations.type-of-licence.gb', 'B']
        ];
    }

    public function testMapPeopleSectionToView()
    {
        $in = [
            [
                'person' => [
                    'title' => [
                        'description' => 'Mr'
                    ],
                    'forename' => 'Foo',
                    'familyName' => 'Bar',
                    'birthDate' => '1980/01/02'
                ]
            ],
            [
                'person' => [
                    'title' => [
                        'description' => 'Doctor'
                    ],
                    'forename' => 'Buz',
                    'familyName' => 'Cake',
                    'birthDate' => '1980/02/01'
                ]
            ]
        ];
        $out = [
            'people' => [
                [
                    ['value' => 'continuations.people-section.table.name_translated', 'header' => true],
                    ['value' => 'continuations.people-section.table.date-of-birth_translated', 'header' => true]
                ],
                [
                    ['value' => 'Doctor Buz Cake'],
                    ['value' => '01/02/1980']
                ],
                [
                    ['value' => 'Mr Foo Bar'],
                    ['value' => '02/01/1980']
                ],
            ],
            'totalPeopleMessage' =>
                'continuations.people.section-header.' . RefData::ORG_TYPE_REGISTERED_COMPANY . '_translated'
        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->assertEquals(
            $out,
            LicenceChecklist::mapPeopleSectionToView($in, RefData::ORG_TYPE_REGISTERED_COMPANY, $mockTranslator)
        );
    }

    public function testMapVehiclesSectionToView()
    {
        $in = [
            'goodsOrPsv' => [
                'id' => RefData::LICENCE_CATEGORY_GOODS_VEHICLE
            ],
            'licenceVehicles' => [
                [
                    'vehicle' => [
                        'vrm' => 'VRM456',
                        'platedWeight' => 1000,
                    ],
                ],
                [
                    'vehicle' => [
                        'vrm' => 'VRM123',
                        'platedWeight' => 2000,
                    ],
                ]
            ]
        ];
        $out = [
            'vehicles' => [
                [
                    ['value' => 'continuations.vehicles-section.table.vrm_translated', 'header' => true],
                    ['value' => 'continuations.vehicles-section.table.weight_translated', 'header' => true],
                ],
                [
                    ['value' => 'VRM123'],
                    ['value' => '2000kg'],
                ],
                [
                    ['value' => 'VRM456'],
                    ['value' => '1000kg'],
                ]
            ],
            'totalVehiclesMessage' => 'continuations.vehicles.section-header_translated',
        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->assertEquals(
            $out,
            LicenceChecklist::mapVehiclesSectionToView($in, $mockTranslator)
        );
    }

    public function testMapOperatingCentresSectionToView()
    {
        $in = [
            'goodsOrPsv' => [
                'id' => RefData::LICENCE_CATEGORY_GOODS_VEHICLE
            ],
            'operatingCentres' => [
                [
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Foo',
                            'town' => 'Bar'
                        ],
                    ],
                    'noOfVehiclesRequired' => 1,
                    'noOfTrailersRequired' => 2,
                ],
                [
                    'operatingCentre' => [
                        'address' => [
                            'addressLine1' => 'Baz',
                            'town' => 'Cake'
                        ],
                    ],
                    'noOfVehiclesRequired' => 3,
                    'noOfTrailersRequired' => 4,
                ]
            ]
        ];
        $out = [
            'operatingCentres' => [
                [
                    ['value' => 'continuations.oc-section.table.oc_translated', 'header' => true],
                    ['value' => 'continuations.oc-section.table.vehicles_translated', 'header' => true],
                    ['value' => 'continuations.oc-section.table.trailers_translated', 'header' => true],
                ],
                [
                    ['value' => 'Baz, Cake'],
                    ['value' => '3'],
                    ['value' => '4'],
                ],
                [
                    ['value' => 'Foo, Bar'],
                    ['value' => '1'],
                    ['value' => '2'],
                ]
            ],
            'totalOperatingCentresMessage' => 'continuations.operating-centres.section-header_translated',
        ];
        $mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->assertEquals(
            $out,
            LicenceChecklist::mapOperatingCentresSectionToView($in, $mockTranslator)
        );
    }
}
