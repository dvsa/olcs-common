<?php

/**
 * Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\OperatingCentres;
use Common\Service\Helper\FlashMessengerHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentresTest extends MockeryTestCase
{
    /**
     * @dataProvider resultProvider
     */
    public function testMapFromResult($result, $expected)
    {
        $this->assertEquals($expected, OperatingCentres::mapFromResult($result));
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
        $form = m::mock(\Zend\Form\Form::class);
        $fm = m::mock(FlashMessengerHelperService::class);
        $translator = m::mock();
        $translator->shouldReceive('translateReplace')->with('CODE_EXTERNAL', ['CODE' => 'MESSAGE'])->once()
            ->andReturn('TRANSLATED');
        $location = 'EXTERNAL';

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
                'trafficArea' => [
                    'TRANSLATED'
                ],
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
            'trafficArea' => [
                'foo' => ['CODE' => 'MESSAGE']
            ],
            'enforcementArea' => [
                'foo' => 'bar8'
            ],
            'cake' => 'bar'
        ];

        $form->shouldReceive('setMessages')
            ->once()
            ->with($expectedMessages);

        $fm->shouldReceive('addCurrentErrorMessage')
            ->once()
            ->with('bar');

        OperatingCentres::mapFormErrors($form, $errors, $fm, $translator, $location);
    }

    public function resultProvider()
    {
        return [
            [
                [
                    'foo' => 'bar',
                    'licence' => [
                        'enforcementArea' => [
                            'id' => 123
                        ]
                    ]
                ],
                [
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
            ]
        ];
    }
}
