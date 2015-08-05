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
                'totAuthSmallVehicles' => [
                    'bar4'
                ],
                'totAuthMediumVehicles' => [
                    'bar5'
                ],
                'totAuthLargeVehicles' => [
                    'bar6'
                ]
            ],
            'table' => [
                'table' => [
                    'bar7'
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
            'totAuthSmallVehicles' => [
                'foo' => 'bar4'
            ],
            'totAuthMediumVehicles' => [
                'foo' => 'bar5'
            ],
            'totAuthLargeVehicles' => [
                'foo' => 'bar6'
            ],
            'operatingCentres' => [
                'foo' => 'bar7'
            ],
            'cake' => 'bar'
        ];

        $form->shouldReceive('setMessages')
            ->once()
            ->with($expectedMessages);

        $fm->shouldReceive('addCurrentErrorMessage')
            ->once()
            ->with('bar');

        OperatingCentres::mapFormErrors($form, $errors, $fm);
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
                        'enforcementArea' => 123
                    ]
                ]
            ],
            [
                [
                    'foo' => 'bar',
                    'enforcementArea' => [
                        'id' => 123
                    ]
                ],
                [
                    'data' => [
                        'foo' => 'bar',
                        'enforcementArea' => [
                            'id' => 123
                        ]
                    ],
                    'dataTrafficArea' => [
                        'enforcementArea' => 123
                    ]
                ]
            ]
        ];
    }
}
