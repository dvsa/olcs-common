<?php

namespace CommonTest\Data\Mapper\Lva;

use Common\Data\Mapper\Lva\OperatingCentre;
use Common\Service\Helper\FlashMessengerHelperService;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\RefData;

/**
 * Operating Centre Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentreTest extends MockeryTestCase
{
    /**
     * @dataProvider adProvider
     */
    public function testMapFromResult($adPlaced, $adPlacedNow, $adPlacedPost, $adPlacedLater)
    {
        $result = [
            'version' => 1,
            'noOfVehiclesRequired' => 10,
            'noOfTrailersRequired' => 11,
            'permission' => 13,
            'operatingCentre' => [
                'foo' => 'bar',
                'address' => [
                    'abc',
                    'countryCode' => ['id' => 'GB']
                ]
            ],
            'adPlaced' => $adPlaced,
            'adPlacedIn' => 'Donny Star',
            'adPlacedDate' => '2015-01-01'
        ];

        $expected = [
            'version' => 1,
            'data' => [
                'noOfVehiclesRequired' => 10,
                'noOfTrailersRequired' => 11,
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
                'adPlaced' => $adPlacedNow,
                'adPlacedPost' => $adPlacedPost,
                'adPlacedLater' => $adPlacedLater,
                'adPlacedIn' => 'Donny Star',
                'adPlacedDate' => '2015-01-01'
            ]
        ];

        $this->assertEquals($expected, OperatingCentre::mapFromResult($result));
    }

    public function adProvider()
    {
        return [
            [RefData::AD_UPLOAD_NOW, RefData::AD_UPLOAD_NOW, null, null],
            [RefData::AD_POST, null, RefData::AD_POST, null],
            [RefData::AD_UPLOAD_LATER, null, null, RefData::AD_UPLOAD_LATER]
        ];
    }

    /**
     * @dataProvider mapFromFormProvider
     */
    public function testMapFromForm($data, $expected)
    {
        $this->assertEquals($expected, OperatingCentre::mapFromForm($data));
    }

    public function mapFromFormProvider()
    {
        return [
            [
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'data' => [
                        'noOfVehiclesRequired' => 10,
                        'noOfTrailersRequired' => 11,
                        'permission' => 'Y'
                    ],
                    'advertisements' => [
                        'adPlacedNow' => RefData::AD_UPLOAD_NOW,
                        'adPlacedIn' => 'Donny Star',
                        'adPlacedDate' => '2015-01-01'
                    ]
                ],
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'noOfVehiclesRequired' => 10,
                    'noOfTrailersRequired' => 11,
                    'permission' => 'Y',
                    'adPlaced' => RefData::AD_UPLOAD_NOW,
                    'adPlacedNow' => RefData::AD_UPLOAD_NOW,
                    'adPlacedIn' => 'Donny Star',
                    'adPlacedDate' => '2015-01-01'
                ]
            ],
            [
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'data' => [
                        'noOfVehiclesRequired' => 10,
                        'noOfTrailersRequired' => 11,
                        'permission' => 'Y'
                    ],
                    'advertisements' => [
                        'adPlacedPost' => RefData::AD_POST,
                        'adPlacedIn' => 'Donny Star',
                        'adPlacedDate' => '2015-01-01'
                    ]
                ],
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'noOfVehiclesRequired' => 10,
                    'noOfTrailersRequired' => 11,
                    'permission' => 'Y',
                    'adPlaced' => RefData::AD_POST,
                    'adPlacedPost' => RefData::AD_POST,
                    'adPlacedIn' => 'Donny Star',
                    'adPlacedDate' => '2015-01-01'
                ]
            ],
            [
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'data' => [
                        'noOfVehiclesRequired' => 10,
                        'noOfTrailersRequired' => 11,
                        'permission' => 'Y'
                    ],
                    'advertisements' => [
                        'adPlacedLater' => RefData::AD_UPLOAD_LATER,
                        'adPlacedIn' => 'Donny Star',
                        'adPlacedDate' => '2015-01-01'
                    ]
                ],
                [
                    'version' => 1,
                    'address' => ['foo' => 'bar'],
                    'noOfVehiclesRequired' => 10,
                    'noOfTrailersRequired' => 11,
                    'permission' => 'Y',
                    'adPlaced' => RefData::AD_UPLOAD_LATER,
                    'adPlacedLater' => RefData::AD_UPLOAD_LATER,
                    'adPlacedIn' => 'Donny Star',
                    'adPlacedDate' => '2015-01-01'
                ]
            ]
        ];
    }

    public function testMapFormErrors()
    {
        $location = 'EXTERNAL';
        $form = m::mock(\Zend\Form\Form::class);
        $fm = m::mock(FlashMessengerHelperService::class);
        $th = m::mock(TranslationHelperService::class);
        $th->shouldReceive('translateReplace')
            ->with('ERR_OC_PC_TA_GB', ['url'])
            ->andReturn('translated');
        $th->shouldReceive('translateReplace')
            ->with('ERR_TA_PSV_SR_EXTERNAL', ['Foo'])
            ->andReturn('translated 2');

        $expectedMessages = [
            'data' => [
                'noOfVehiclesRequired' => [
                    'bar1'
                ],
                'noOfTrailersRequired' => [
                    'bar2'
                ],
                'permission' => [
                    'bar6'
                ]
            ],
            'advertisements' => [
                'adPlacedIn' => [
                    'bar3'
                ],
                'adPlacedDate' => [
                    'bar4'
                ]
            ],
            'address' => [
                'postcode' => [
                    [
                        'ERR_OC_PC_TA_GB' => 'translated',
                        'ERR_TA_PSV_SR' => 'translated 2',
                    ]
                ]
            ]
        ];

        $errors = [
            'postcode' => [
                [
                    'ERR_OC_PC_TA_GB' => '{"current":"Foo","oc":"Bar"}',
                    'ERR_TA_PSV_SR' => 'Foo',
                ]
            ],
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
            'permission' => [
                'foo' => 'bar6'
            ],
            'cake' => 'bar'
        ];

        $form->shouldReceive('setMessages')
            ->once()
            ->with($expectedMessages);

        $fm->shouldReceive('addCurrentErrorMessage')
            ->once()
            ->with('bar');

        OperatingCentre::mapFormErrors($form, $errors, $fm, $th, $location, 'url');
    }

    /**
     * @dataProvider mapFromPostProvider
     */
    public function testMapFromPost($data, $expected)
    {
        $this->assertEquals($expected, OperatingCentre::mapFromPost($data));
    }

    public function mapFromPostProvider()
    {
        return [
            [
                [
                    'advertisements' => [
                        'adPlaced' => RefData::AD_POST,
                        'file' => [
                            'list' => ['foo']
                        ]
                    ],
                    'bar' => 'cake'
                ],
                [
                    'advertisements' => [
                        'adPlaced' => null,
                        'adPlacedLater' => null,
                        'adPlacedPost' => RefData::AD_POST,
                        'uploadedFileCount' => 1,
                        'file' => [
                            'list' => ['foo']
                        ]
                    ],
                    'bar' => 'cake'
                ]
            ],
            [
                [
                    'advertisements' => [
                        'adPlaced' => RefData::AD_UPLOAD_LATER,
                    ],
                    'bar' => 'cake'
                ],
                [
                    'advertisements' => [
                        'adPlaced' => null,
                        'adPlacedLater' => RefData::AD_UPLOAD_LATER,
                        'adPlacedPost' => null,
                        'uploadedFileCount' => 0,
                    ],
                    'bar' => 'cake'
                ]
            ],
            [
                [
                    'advertisements' => [
                        'adPlaced' => RefData::AD_UPLOAD_NOW,
                    ],
                    'bar' => 'cake'
                ],
                [
                    'advertisements' => [
                        'adPlaced' => RefData::AD_UPLOAD_NOW,
                        'adPlacedLater' => null,
                        'adPlacedPost' => null,
                        'uploadedFileCount' => 0,
                    ],
                    'bar' => 'cake'
                ]
            ],
        ];
    }
}
