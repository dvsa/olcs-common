<?php

namespace CommonTest\Data\Mapper;

use Common\Data\Mapper\LicenceChecklist;
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
            ]
        ];
        $out = [
            'typeOfLicence' => [
                'operatingFrom' => $key . '_translated',
                'goodsOrPsv' => 'Bar',
                'licenceType' => 'Cake'
            ],
            'businessType' => [
                'typeOfBusiness' => 'Limited Company'
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
                        'weight' => 2000,
                    ],
                    [
                        'vrm' => 'VRM456',
                        'weight' => 1000,
                    ]
                ],
                'header' => 'continuations.vehicles-section-header_translated',
                'isGoods' => true,
                'displayVehiclesCount' => 2
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
                    ['value' => 2000],
                ],
                [
                    ['value' => 'VRM456'],
                    ['value' => 1000],
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
}
