<?php

/**
 * Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\OperatingCentre;
use Common\Service\Helper\FlashMessengerHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTest extends MockeryTestCase
{
    public function testMapFromResult()
    {
        $result = [
            'version' => 1,
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 11,
            'sufficientParking' => 12,
            'permission' => 13,
            'operatingCentre' => [
                'foo' => 'bar',
                'address' => [
                    'abc',
                    'countryCode' => ['id' => 'GB']
                ]
            ],
            'adPlaced' => 'Y',
            'adPlacedIn' => 'Donny Star',
            'adPlacedDate' => '2015-01-01'
        ];

        $expected = [
            'version' => 1,
            'data' => [
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 11,
                'sufficientParking' => 12,
                'permission' => 13,
            ],
            'operatingCentre' => [
                'foo' => 'bar',
                'address' => [
                    'abc',
                    'countryCode' => ['id' => 'GB']
                ]
            ],
            'address' => [
                'abc',
                'countryCode' => 'GB'
            ],
            'advertisements' => [
                'adPlaced' => 'Y',
                'adPlacedIn' => 'Donny Star',
                'adPlacedDate' => '2015-01-01'
            ]
        ];

        $this->assertEquals($expected, OperatingCentre::mapFromResult($result));
    }

    public function testMapFromForm()
    {
        $data = [
            'version' => 1,
            'address' => ['foo' => 'bar'],
            'data' => [
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 11,
                'sufficientParking' => 'Y',
                'permission' => 'Y'
            ],
            'advertisements' => [
                'adPlaced' => 'Y',
                'adPlacedIn' => 'Donny Star',
                'adPlacedDate' => '2015-01-01'
            ]
        ];

        $expected = [
            'version' => 1,
            'address' => ['foo' => 'bar'],
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 11,
            'sufficientParking' => 'Y',
            'permission' => 'Y',
            'adPlaced' => 'Y',
            'adPlacedIn' => 'Donny Star',
            'adPlacedDate' => '2015-01-01'
        ];

        $this->assertEquals($expected, OperatingCentre::mapFromForm($data));
    }

    public function testMapFormErrors()
    {
        $form = m::mock(\Zend\Form\Form::class);
        $fm = m::mock(FlashMessengerHelperService::class);

        $expectedMessages = [
            'data' => [
                'noOfVehiclesRequired' => [
                    'bar1'
                ],
                'noOfTrailersRequired' => [
                    'bar2'
                ]
            ],
            'advertisements' => [
                'adPlacedIn' => [
                    'bar3'
                ],
                'adPlacedDate' => [
                    'bar4'
                ]
            ]
        ];

        $errors = [
            'noOfVehiclesRequired' => [
                'foo' => 'bar1'
            ],
            'noOfTrailersRequired' => [
                'foo' => 'bar2'
            ],
            'adPlacedIn' => [
                'foo' => 'bar3'
            ],
            'adPlacedDate' => [
                'foo' => 'bar4'
            ],
            'cake' => 'bar'
        ];

        $form->shouldReceive('setMessages')
            ->once()
            ->with($expectedMessages);

        $fm->shouldReceive('addCurrentErrorMessage')
            ->once()
            ->with('bar');

        OperatingCentre::mapFormErrors($form, $errors, $fm);
    }
}
