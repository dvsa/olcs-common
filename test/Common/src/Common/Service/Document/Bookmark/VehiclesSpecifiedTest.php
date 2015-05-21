<?php
namespace CommonTest\Service\Document\Bookmark;

use Common\Service\Document\Bookmark\VehiclesSpecified;
use Common\Service\Entity\LicenceEntityService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Entity\VehicleEntityService;

/**
 * VehiclesSpecified bookmark test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehiclesSpecifiedTest extends MockeryTestCase
{
    public function testGetQueryContainsExpectedKeys()
    {
        $bookmark = new VehiclesSpecified();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertEquals('Licence', $query['service']);

        $this->assertEquals(
            [
                'id' => 123
            ],
            $query['data']
        );
    }

    public function testRenderWithNoVehiclesSpecified()
    {
        $bookmark = new VehiclesSpecified();
        $bookmark->setData([]);

        $this->assertEquals(
            '',
            $bookmark->render()
        );
    }

    public function testRenderWithGoodsVehiclesSpecified()
    {
        $bookmark = m::mock('Common\Service\Document\Bookmark\VehiclesSpecified')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('CHECKLIST_3CELL_TABLE')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'licenceVehicles' => [
                    [
                        'vehicle' => [
                            'vrm' => 'VRM1',
                            'platedWeight' => 900
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM4',
                            'platedWeight' => 1000
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM3',
                            'platedWeight' => 1200
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM2',
                            'platedWeight' => 2000
                        ]
                    ],
                ],
                'goodsOrPsv' => [
                    'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                ]
            ]
        );

        $header = [
            'BOOKMARK1' => 'Registration mark',
            'BOOKMARK2' => 'Plated weight',
            'BOOKMARK3' => 'To continue to be specified on licence (Y/N)'
        ];
        $row1 = [
            'BOOKMARK1' => 'VRM1',
            'BOOKMARK2' => 900,
            'BOOKMARK3' => ''
        ];
        $row2 = [
            'BOOKMARK1' => 'VRM2',
            'BOOKMARK2' => 2000,
            'BOOKMARK3' => ''
        ];
        $row3 = [
            'BOOKMARK1' => 'VRM3',
            'BOOKMARK2' => 1200,
            'BOOKMARK3' => ''
        ];
        $row4 = [
            'BOOKMARK1' => 'VRM4',
            'BOOKMARK2' => 1000,
            'BOOKMARK3' => ''
        ];
        $emptyRow = [
            'BOOKMARK1' => '',
            'BOOKMARK2' => '',
            'BOOKMARK3' => ''
        ];

        $mockParser = m::mock('Common\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $header)
            ->andReturn('header|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row1)
            ->andReturn('row1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row2)
            ->andReturn('row2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row3)
            ->andReturn('row3|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row4)
            ->andReturn('row4|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $emptyRow)
            ->andReturn('emptyrow|')
            ->times(11)
            ->getMock();

        $bookmark->setParser($mockParser);

        $rendered = 'header|row1|row2|row3|row4|' . str_repeat('emptyrow|', 11);
        $this->assertEquals(
            $rendered,
            $bookmark->render()
        );
    }

    public function testRenderWithPsvVehiclesSpecified()
    {
        $bookmark = m::mock('Common\Service\Document\Bookmark\VehiclesSpecified')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('getSnippet')
            ->with('CHECKLIST_3CELL_TABLE')
            ->andReturn('snippet')
            ->once()
            ->getMock();

        $bookmark->setData(
            [
                'licenceVehicles' => [
                    [
                        'vehicle' => [
                            'vrm' => 'VRM1',
                            'psvType' => [
                                'id' => VehicleEntityService::PSV_TYPE_SMALL
                            ]
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM4',
                            'psvType' => [
                                'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                            ]
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM3',
                            'psvType' => [
                                'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                            ]
                        ]
                    ],
                    [
                        'vehicle' => [
                            'vrm' => 'VRM4',
                            'psvType' => [
                                'id' => VehicleEntityService::PSV_TYPE_LARGE
                            ]
                        ]
                    ],
                ],
                'goodsOrPsv' => [
                    'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                ]
            ]
        );

        $header = [
            'BOOKMARK1' => 'Registration mark',
            'BOOKMARK2' => 'Vehicle type',
            'BOOKMARK3' => 'To continue to be specified on licence (Y/N)'
        ];
        $row1 = [
            'BOOKMARK1' => 'VRM1',
            'BOOKMARK2' => 'Max 8 seats',
            'BOOKMARK3' => ''
        ];
        $row2 = [
            'BOOKMARK1' => 'VRM3',
            'BOOKMARK2' => '9 to 16 seats',
            'BOOKMARK3' => ''
        ];
        $row3 = [
            'BOOKMARK1' => 'VRM4',
            'BOOKMARK2' => 'Over 16 seats',
            'BOOKMARK3' => ''
        ];
        $row4 = [
            'BOOKMARK1' => 'VRM4',
            'BOOKMARK2' => '9 to 16 seats',
            'BOOKMARK3' => ''
        ];
        $emptyRow = [
            'BOOKMARK1' => '',
            'BOOKMARK2' => '',
            'BOOKMARK3' => ''
        ];

        $mockParser = m::mock('Common\Service\Document\Parser\RtfParser')
            ->shouldReceive('replace')
            ->with('snippet', $header)
            ->andReturn('header|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row1)
            ->andReturn('row1|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row2)
            ->andReturn('row2|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row3)
            ->andReturn('row3|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $row4)
            ->andReturn('row4|')
            ->once()
            ->shouldReceive('replace')
            ->with('snippet', $emptyRow)
            ->andReturn('emptyrow|')
            ->times(11)
            ->getMock();

        $bookmark->setParser($mockParser);

        $rendered = 'header|row1|row2|row3|row4|' . str_repeat('emptyrow|', 11);
        $this->assertEquals(
            $rendered,
            $bookmark->render()
        );
    }
}
