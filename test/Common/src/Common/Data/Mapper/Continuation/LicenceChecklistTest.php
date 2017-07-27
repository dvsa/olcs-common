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
    protected $mockTranslator;

    public function setUp()
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();
    }
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
                    'description' => 'Cake',
                    'id' => RefData::LICENCE_TYPE_STANDARD_INTERNATIONAL
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
                                'birthDate' => null
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
                ],
                'tmLicences' => [
                    [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'title' => [
                                        'description' => 'Mr'
                                    ],
                                    'forename' => 'foo',
                                    'familyName' => 'bar',
                                    'birthDate' => '1980-01-01'
                                ]
                            ]
                        ]
                    ],
                    [
                        'transportManager' => [
                            'homeCd' => [
                                'person' => [
                                    'title' => [
                                        'description' => 'Mr'
                                    ],
                                    'forename' => 'cake',
                                    'familyName' => 'baz',
                                    'birthDate' => null
                                ]
                            ]
                        ]
                    ],
                ],
                'workshops' => [
                    [
                        'contactDetails' => [
                            'fao' => 'Foo Bar',
                            'address' => [
                                'addressLine1' => 'Line2',
                                'town' => 'Town2'
                            ]
                        ],
                        'isExternal' => 'N'
                    ],
                    [
                        'contactDetails' => [
                            'fao' => 'Baz Cake',
                            'address' => [
                                'addressLine1' => 'Line1',
                                'town' => 'Town1'
                            ]
                        ],
                        'isExternal' => 'Y'
                    ],
                ],
                'safetyInsVehicles' => 2,
                'safetyInsTrailers' => 2,
                'safetyInsVaries' => 'N',
                'tachographIns' => [
                    'id' => 'tach_external'
                ],
                'tachographInsName' => 'Foo Ltd',
            ],
            'ocChanges' => 1,
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
                            'birthDate' => ''
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
                    'showEstablishmentAddress' => true,
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
                    'displayOperatingCentresCount' => RefData::CONTINUATIONS_DISPLAY_OPERATING_CENTRES_COUNT,
                    'ocChanges' => 1
                ],
                'transportManagers' => [
                    'transportManagers' => [
                        [
                            'name' => 'Mr cake baz',
                            'dob' => ''
                        ],
                        [
                            'name' => 'Mr foo bar',
                            'dob' => '01/01/1980'
                        ]
                    ],
                    'totalTransportManagers' => 2,
                    'displayTransportManagersCount' => RefData::CONTINUATIONS_DISPLAY_TM_COUNT
                ],
                'safety' => [
                    'safetyInspectors' => [
                        [
                            'name' => 'Baz Cake (continuations.safety-section.table.external-contractor_translated)',
                            'address' => 'Line1, Town1'
                        ],
                        [
                            'name' => 'Foo Bar (continuations.safety-section.table.owner-or-employee_translated)',
                            'address' => 'Line2, Town2'
                        ]
                    ],
                    'totalSafetyInspectors' => 2,
                    'safetyInsVehicles' => '2 continuations.safety-section.table.weeks_translated',
                    'safetyInsTrailers' => '2 continuations.safety-section.table.weeks_translated',
                    'safetyInsVaries' => 'No_translated',
                    'tachographIns' => 'continuations.safety-section.table.tach_external_translated',
                    'tachographInsName' => 'Foo Ltd',
                    'isGoods' => true,
                    'showCompany' => true,
                    'displaySafetyInspectorsCount' => RefData::CONTINUATIONS_DISPLAY_SAFETY_INSPECTORS_COUNT
                ],
                'continuationDetailId' => 999,
            ],
        ];

        $this->assertEquals($out, LicenceChecklist::mapFromResultToView($in, $this->mockTranslator));
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
                    'birthDate' => null
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
                    ['value' => '']
                ],
                [
                    ['value' => 'Mr Foo Bar'],
                    ['value' => '02/01/1980']
                ],
            ],
            'totalPeopleMessage' =>
                'continuations.people.section-header.' . RefData::ORG_TYPE_REGISTERED_COMPANY . '_translated'
        ];

        $this->assertEquals(
            $out,
            LicenceChecklist::mapPeopleSectionToView($in, RefData::ORG_TYPE_REGISTERED_COMPANY, $this->mockTranslator)
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

        $this->assertEquals($out, LicenceChecklist::mapVehiclesSectionToView($in, $this->mockTranslator));
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

        $this->assertEquals($out, LicenceChecklist::mapOperatingCentresSectionToView($in, $this->mockTranslator));
    }

    public function testMapTransportManagerSectionToView()
    {
        $in = [
            'tmLicences' => [
                [
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'title' => [
                                    'description' => 'Mr'
                                ],
                                'forename' => 'foo',
                                'familyName' => 'bar',
                                'birthDate' => '1980-01-01'
                            ],
                        ],
                    ],
                ],
                [
                    'transportManager' => [
                        'homeCd' => [
                            'person' => [
                                'title' => [
                                    'description' => 'Mr'
                                ],
                                'forename' => 'baz',
                                'familyName' => 'cake',
                                'birthDate' => null
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $out = [
            'transportManagers' => [
                [
                    ['value' => 'continuations.tm-section.table.name_translated', 'header' => true],
                    ['value' => 'continuations.tm-section.table.dob_translated', 'header' => true],
                ],
                [
                    ['value' => 'Mr baz cake'],
                    ['value' => ''],
                ],
                [
                    ['value' => 'Mr foo bar'],
                    ['value' => '01/01/1980'],
                ]
            ],
            'totalTransportManagersMessage' => 'continuations.tm.section-header_translated',
        ];

        $this->assertEquals($out, LicenceChecklist::mapTransportManagerSectionToView($in, $this->mockTranslator));
    }

    public function testMapSafetyInspectorsSectionToView()
    {
        $in = [
            'workshops' => [
                [
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Line 2',
                            'town' => 'Town 2'
                        ],
                        'fao' => 'Foo Bar'
                    ],
                    'isExternal' => 'Y'
                ],
                [
                    'contactDetails' => [
                        'address' => [
                            'addressLine1' => 'Line 1',
                            'town' => 'Town 1'
                        ],
                        'fao' => 'Baz Cake'
                    ],
                    'isExternal' => 'N'
                ],
            ]
        ];
        $out = [
            'safetyInspectors' => [
                [
                    ['value' => 'continuations.safety-section.table.inspector_translated', 'header' => true],
                    ['value' => 'continuations.safety-section.table.address_translated', 'header' => true],
                ],
                [
                    ['value' => 'Baz Cake (continuations.safety-section.table.owner-or-employee_translated)'],
                    ['value' => 'Line 1, Town 1'],
                ],
                [
                    ['value' => 'Foo Bar (continuations.safety-section.table.external-contractor_translated)'],
                    ['value' => 'Line 2, Town 2'],
                ]
            ],
            'totalSafetyInspectorsMessage' => 'continuations.safety.section-header_translated',
        ];

        $this->assertEquals($out, LicenceChecklist::mapSafetyInspectorsSectionToView($in, $this->mockTranslator));
    }
}
