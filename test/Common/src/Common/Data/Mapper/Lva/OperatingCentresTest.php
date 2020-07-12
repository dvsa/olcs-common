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
    const LOCATION = 'EXTERNAL';
    const TRANSL = '_TRANSL_';

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
                    ]
                ],
                'expected' => [
                    'data' => [
                        'foo' => 'bar',
                        'licence' => [
                            'enforcementArea' => [
                                'id' => 123
                            ]
                        ]
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
                    ]
                ],
                [
                    'data' => [
                        'foo' => 'bar',
                        'enforcementArea' => [
                            'id' => 123
                        ],
                        'licence' => [
                            'trafficArea' => ['id' => 'X']
                        ]
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
                    'trafficArea' => ['id' => 'X']
                ],
                [
                    'data' => [
                        'foo' => 'bar',
                        'enforcementArea' => [
                            'id' => 123
                        ],
                        'trafficArea' => ['id' => 'X']
                    ],
                    'dataTrafficArea' => [
                        'trafficArea' => 'X',
                        'enforcementArea' => 123
                    ]
                ]
            ]
        ];
    }

    public function testMapFromForm()
    {
        $formData = [
            'data' => [
                'foo' => 'bar'
            ],
            'dataTrafficArea' => [
                'bar' => 'cake'
            ]
        ];

        $expected = [
            'foo' => 'bar',
            'bar' => 'cake'
        ];

        $this->assertEquals($expected, OperatingCentres::mapFromForm($formData));
    }

    public function testMapFormErrors()
    {
        $expectedMessages = [
            'data' => [
                'totCommunityLicences' => [
                    'bar1'
                ],
                'totAuthVehicles' => [
                    'bar2'
                ],
                'totAuthTrailers' => [
                    'bar3'
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
            'totAuthVehicles' => [
                'foo' => 'bar2'
            ],
            'totAuthTrailers' => [
                'foo' => 'bar3'
            ],
            'operatingCentres' => [
                'foo' => 'bar7'
            ],
            'enforcementArea' => [
                'foo' => 'bar8'
            ],
            'detach_error' => 'unit_ERR_MSG',
        ];

        $form = m::mock(\Zend\Form\Form::class);
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
