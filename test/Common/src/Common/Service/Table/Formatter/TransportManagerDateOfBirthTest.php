<?php

namespace CommonTest\Service\Table\Formatter;

use Common\RefData;
use Common\Service\Table\Formatter\TransportManagerDateOfBirth;
use Laminas\View\HelperPluginManager;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class TransportManagerDateOfBirthTest extends MockeryTestCase
{
    protected $viewHelperManager;
    protected $sut;

    protected function setUp(): void
    {
        $this->viewHelperManager = m::mock(HelperPluginManager::class);
        $this->sut = new TransportManagerDateOfBirth($this->viewHelperManager);
    }

    protected function mockGetStatusHtml($expectedStatusId, $expectedStatusDescription, $statusHtml = '<STATUS HTML>')
    {
        $mockViewHelper = m::mock();

        $this->viewHelperManager->shouldReceive('get')
            ->with('transportManagerApplicationStatus')
            ->once()
            ->andReturn($mockViewHelper);

        $mockViewHelper->shouldReceive('render')
            ->with($expectedStatusId, $expectedStatusDescription)
            ->once()
            ->andReturn($statusHtml);
    }

    /**
     * Provider for testFormat
     */
    public function providerFormat()
    {
        return [
            [ // NoLvaNorInternal

                [
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => ['name' => 'dob']
                ],
                '01/12/1980'
            ],
            [ // LvaWithoutInternal

                [
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'application'
                    ]
                ],
                '01/12/1980'
            ],
            [ // NoLvaWithInternal

                [
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'internal' => true
                    ]
                ],
                '01/12/1980'
            ],
            [ // ApplicationInternal

                [
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ]
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'application',
                        'internal' => true,
                    ]
                ],
                '<span class="nowrap">01/12/1980 <STATUS HTML></span>'
            ],
            [ // ApplicationExternal

                [
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ]
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'application',
                        'internal' => false,
                    ]
                ],
                '<span class="nowrap">01/12/1980 <STATUS HTML></span>'
            ],
            [ // VariationInternal

                [
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ],
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'variation',
                        'internal' => true,
                    ]
                ],
                '<span class="nowrap">01/12/1980 <STATUS HTML></span>'
            ],
            [ // VariationExternal

                [
                    'data' => [
                        'dob' => '1980-12-01',
                        'status' => [
                            'id' => RefData::TMA_STATUS_POSTAL_APPLICATION,
                            'description' => 'status description',
                        ],
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'variation',
                        'internal' => false,
                    ]
                ],
                '<span class="nowrap">01/12/1980 <STATUS HTML></span>'
            ],
            [ // LicenceInternal

                [
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'licence',
                        'internal' => true,
                    ]
                ],
                '01/12/1980'
            ],
            [ // LicenceExternal

                [
                    'data' => [
                        'dob' => '1980-12-01'
                    ],
                    'column' => [
                        'name' => 'dob',
                        'lva' => 'licence',
                        'internal' => false,
                    ]
                ],
                '01/12/1980'
            ]
        ];
    }

    /**
     * @dataProvider providerFormat
     */
    public function testFormat($testData, $expected)
    {
        if (isset($testData['data']['status'])) {
            $this->mockGetStatusHtml($testData['data']['status']['id'], $testData['data']['status']['description']);
        }

        $this->assertEquals($expected, $this->sut->format($testData['data'], $testData['column']));
    }
}
