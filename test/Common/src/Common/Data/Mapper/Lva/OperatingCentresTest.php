<?php

namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\OperatingCentres;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers \Common\Data\Mapper\Lva\OperatingCentres
 */
class OperatingCentresTest extends MockeryTestCase
{
    public const LOCATION = 'EXTERNAL';
    public const TRANSL = '_TRANSL_';

    /** @var  m\MockInterface | TranslationHelperService*/
    private $mockTranslator;
    /** @var  m\MockInterface | FlashMessengerHelperService */
    private $mockFlashMsg;

    public function setUp(): void
    {
        $this->mockTranslator = m::mock(TranslationHelperService::class);
        $this->mockFlashMsg = m::mock(FlashMessengerHelperService::class);
    }

    /**
     * @dataProvider dpTestMapFromResult
     */
    public function testMapFromResult($result, $expected)
    {
        $this->assertEquals($expected, OperatingCentres::mapFromResult($result));
    }

    public function dpTestMapFromResult()
    {
        return [
            [
                'result' => [
                    'foo' => 'bar',
                    'licence' => [
                        'enforcementArea' => [
                            'id' => 123
                        ]
                    ],
                    'totAuthHgvVehicles' => null,
                    'totAuthLgvVehicles' => 0,
                    'totAuthTrailers' => 1,
                    'totCommunityLicences' => 2,
                ],
                'expected' => [
                    'data' => [
                        'foo' => 'bar',
                        'licence' => [
                            'enforcementArea' => [
                                'id' => 123
                            ]
                        ],
                        'totAuthHgvVehiclesFieldset' => ['totAuthHgvVehicles' => null],
                        'totAuthLgvVehiclesFieldset' => ['totAuthLgvVehicles' => 0],
                        'totAuthTrailersFieldset' => ['totAuthTrailers' => 1],
                        'totCommunityLicencesFieldset' => ['totCommunityLicences' => 2],
                    ],
                    'dataTrafficArea' => [
                        'trafficArea' => null,
                        'enforcementArea' => 123
                    ]
                ]
            ],
            [
                [
                    'foo' => 'bar',
                    'enforcementArea' => [
                        'id' => 123
                    ],
                    'licence' => [
                        'trafficArea' => ['id' => 'X']
                    ],
                    'totAuthHgvVehicles' => null,
                    'totAuthLgvVehicles' => 0,
                    'totAuthTrailers' => 1,
                    'totCommunityLicences' => 2,
                ],
                [
                    'data' => [
                        'foo' => 'bar',
                        'enforcementArea' => [
                            'id' => 123
                        ],
                        'licence' => [
                            'trafficArea' => ['id' => 'X']
                        ],
                        'totAuthHgvVehiclesFieldset' => ['totAuthHgvVehicles' => null],
                        'totAuthLgvVehiclesFieldset' => ['totAuthLgvVehicles' => 0],
                        'totAuthTrailersFieldset' => ['totAuthTrailers' => 1],
                        'totCommunityLicencesFieldset' => ['totCommunityLicences' => 2],
                    ],
                    'dataTrafficArea' => [
                        'trafficArea' => 'X',
                        'enforcementArea' => 123
                    ]
                ]
            ],
            [
                [
                    'foo' => 'bar',
                    'enforcementArea' => [
                        'id' => 123
                    ],
                    'trafficArea' => ['id' => 'X'],
                    'totAuthHgvVehicles' => null,
                    'totAuthLgvVehicles' => 0,
                    'totAuthTrailers' => 1,
                    'totCommunityLicences' => 2,
                ],
                [
                    'data' => [
                        'foo' => 'bar',
                        'enforcementArea' => [
                            'id' => 123
                        ],
                        'trafficArea' => ['id' => 'X'],
                        'totAuthHgvVehiclesFieldset' => ['totAuthHgvVehicles' => null],
                        'totAuthLgvVehiclesFieldset' => ['totAuthLgvVehicles' => 0],
                        'totAuthTrailersFieldset' => ['totAuthTrailers' => 1],
                        'totCommunityLicencesFieldset' => ['totCommunityLicences' => 2],
                    ],
                    'dataTrafficArea' => [
                        'trafficArea' => 'X',
                        'enforcementArea' => 123
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider dpMapFromForm
     */
    public function testMapFromForm($formData, $expected)
    {
        $this->assertEquals($expected, OperatingCentres::mapFromForm($formData));
    }

    public function dpMapFromForm()
    {
        return [
            'all fieldsets included' => [
                'formData' => [
                    'data' => [
                        'foo' => 'bar',
                        'totAuthHgvVehiclesFieldset' => ['totAuthHgvVehicles' => null],
                        'totAuthLgvVehiclesFieldset' => ['totAuthLgvVehicles' => 0],
                        'totAuthTrailersFieldset' => ['totAuthTrailers' => 1],
                        'totCommunityLicencesFieldset' => ['totCommunityLicences' => 2],
                    ],
                    'dataTrafficArea' => [
                        'bar' => 'cake'
                    ]
                ],
                'expected' => [
                    'foo' => 'bar',
                    'bar' => 'cake',
                    'totAuthHgvVehicles' => null,
                    'totAuthLgvVehicles' => 0,
                    'totAuthTrailers' => 1,
                    'totCommunityLicences' => 2,
                ],
            ],
            'all fieldsets removed' => [
                'formData' => [
                    'data' => [
                        'foo' => 'bar',
                    ],
                ],
                'expected' => [
                    'foo' => 'bar',
                ],
            ],
        ];
    }

    public function testMapFormErrors()
    {
        $expectedMessages = [
            'data' => [
                'totCommunityLicencesFieldset' => [
                    'totCommunityLicences' => [
                        'bar1'
                    ],
                ],
                'totAuthHgvVehiclesFieldset' => [
                    'totAuthHgvVehicles' => [
                        'bar2'
                    ],
                ],
                'totAuthLgvVehiclesFieldset' => [
                    'totAuthLgvVehicles' => [
                        'bar3'
                    ],
                ],
                'totAuthTrailersFieldset' => [
                    'totAuthTrailers' => [
                        'bar4'
                    ],
                ],
            ],
            'table' => [
                'table' => [
                    'bar7'
                ]
            ],
            'dataTrafficArea' => [
                'enforcementArea' => [
                    'bar8'
                ]
            ]
        ];

        $errors = [
            'totCommunityLicences' => [
                'foo' => 'bar1'
            ],
            'totAuthHgvVehicles' => [
                'foo' => 'bar2'
            ],
            'totAuthLgvVehicles' => [
                'foo' => 'bar3'
            ],
            'totAuthTrailers' => [
                'foo' => 'bar4'
            ],
            'operatingCentres' => [
                'foo' => 'bar7'
            ],
            'enforcementArea' => [
                'foo' => 'bar8'
            ],
            'detach_error' => 'unit_ERR_MSG',
        ];

        $form = m::mock(\Laminas\Form\Form::class);
        $form->shouldReceive('setMessages')->once()->with($expectedMessages);

        $this->mockFlashMsg->shouldReceive('addCurrentErrorMessage')->once()->with('unit_ERR_MSG');

        OperatingCentres::mapFormErrors($form, $errors, $this->mockFlashMsg, $this->mockTranslator, self::LOCATION);
    }

    public function testMapApiErrors()
    {
        $this->mockTranslator
            ->shouldReceive('translateReplace')
            ->andReturnUsing(
                function ($key, $args) {
                    static::assertEquals(key($args) . '_' . self::LOCATION, $key);

                    return self::TRANSL . current($args);
                }
            );

        $errors = [
            'detach_error' => 'unit_DETACH_ERR_MSG',
            'fieldset' => [
                'field' => 'unit_FLD_ERR_MSG',
            ],
            'trafficArea' => [
                ['CODE' => 'MESSAGE'],
                ['ERR_TA_GOODS' => 'unit_TA_GOODS_msg'],
                ['ERR_TA_PSV' => 'unit_TA_PSV_msg'],
                ['ERR_TA_PSV_SR' => 'unit_TA_PSV_RS_msg'],
            ],
        ];

        $this->mockFlashMsg
            ->shouldReceive('addCurrentErrorMessage')->once()->with('unit_FLD_ERR_MSG')
            ->shouldReceive('addCurrentErrorMessage')->once()->with('unit_DETACH_ERR_MSG')
            ->shouldReceive('addCurrentErrorMessage')->once()->with('MESSAGE')
            ->shouldReceive('addCurrentErrorMessage')->once()->with(self::TRANSL . 'unit_TA_GOODS_msg')
            ->shouldReceive('addCurrentErrorMessage')->once()->with(self::TRANSL . 'unit_TA_PSV_msg')
            ->shouldReceive('addCurrentErrorMessage')->once()->with(self::TRANSL . 'unit_TA_PSV_RS_msg');

        OperatingCentres::mapApiErrors(self::LOCATION, $errors, $this->mockFlashMsg, $this->mockTranslator);
    }

    public function testMapApiErrorsEmpty()
    {
        static::assertNull(
            OperatingCentres::mapApiErrors(self::LOCATION, [], $this->mockFlashMsg, $this->mockTranslator)
        );
    }
}
