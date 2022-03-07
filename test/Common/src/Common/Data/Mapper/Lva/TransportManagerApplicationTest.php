<?php

namespace CommonTest\Data\Mapper\Lva;

use Common\RefData;
use Common\Service\Helper\TranslationHelperService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Data\Mapper\Lva\TransportManagerApplication;

class TransportManagerApplicationTest extends MockeryTestCase
{
    public function testMapFromError()
    {
        $formMessages = [
            'data' => [
                'registeredUser' => [['error']]
            ]
        ];
        $globalMessages = [
            'global' => ['message']
        ];
        $messages = [
            'registeredUser' => ['error'],
            'global' => ['message']
        ];
        $mockForm = m::mock()
            ->shouldReceive('setMessages')
            ->with($formMessages)
            ->once()
            ->getMock();

        $errors = TransportManagerApplication::mapFromErrors($mockForm, $messages);
        $this->assertEquals($errors, $globalMessages);
    }

    /**
     * testMapForSections
     *
     * @param $data
     *
     * @dataProvider transportManagerDataProvider
     */
    public function testMapForSections($data)
    {
        $translationHelper = m::mock(TranslationHelperService::class);

        $translationHelper->shouldReceive(
            'translateReplace'
        )->twice()->andReturn('__TEST__');
        $translationHelper->shouldReceive(
            'translate'
        )->times(23)->andReturn('__TEST__');
        $data = TransportManagerApplication::mapForSections($data, $translationHelper);
        $this->assertIsArray($data);
    }

    public function transportManagerDataProvider()
    {
        return [
            [
                [
                    'application' => [
                        'vehicleType' => [
                            'id' => RefData::APP_VEHICLE_TYPE_MIXED,
                        ],
                    ],
                    'isOwner' => '__TEST__',
                    'tmType' => ['description' => '__TEST__'],
                    'hoursMon' => '__TEST__',
                    'hoursTue' => '__TEST__',
                    'hoursWed' => '__TEST__',
                    'hoursThu' => '__TEST__',
                    'hoursFri' => '__TEST__',
                    'hoursSat' => '__TEST__',
                    'hoursSun' => '__TEST__',
                    'otherLicences' => [
                    ],
                    'additionalInformation'=>'__TEST__',
                    'hasUndertakenTraining' => 'N',
                    'transportManager' =>
                        [
                            'otherLicences' =>[],
                            'employments'=>[],
                            'previousConvictions' => [],
                            'documents' => [],
                            'homeCd' => [
                                'emailAddress' => '__TEST__',
                                'address' => [
                                    'countryCode' => [
                                        'countryDesc' => '__TEST__'
                                    ],
                                ],
                                'person' => [
                                    'forename' => '__TEST__',
                                    'familyName' => '__TEST__',
                                ]
                            ],
                            'workCd' => [
                                'address' => [
                                    'countryCode' => [
                                        'countryDesc' => '__TEST__'
                                    ],
                                ]
                            ]
                        ]
                ]
            ]
        ];
    }
}
