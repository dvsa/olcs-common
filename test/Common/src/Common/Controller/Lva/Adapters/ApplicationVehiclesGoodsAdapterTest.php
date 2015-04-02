<?php

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationVehiclesGoodsAdapter;

/**
 * Application Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetFormData()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'N'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'N'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }

    public function testGetFormDataWithoutHasEnteredReg()
    {
        $id = 3;
        $stubbedResponse = [
            'foo' => 'bar',
            'version' => 5,
            'hasEnteredReg' => 'ABC'
        ];
        $expectedData = [
            'data' => [
                'version' => 5,
                'hasEnteredReg' => 'Y'
            ]
        ];

        $mockApplicationEntity = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationEntity);

        $mockApplicationEntity->shouldReceive('getHeaderData')
            ->with($id)
            ->andReturn($stubbedResponse);

        $this->assertEquals($expectedData, $this->sut->getFormData($id));
    }

    /**
     * @dataProvider providerFormatFilters
     */
    public function testFormatFilters($query, $expected)
    {
        $this->assertEquals($expected, $this->sut->formatFilters($query));
    }

    public function testGetFilteredVehiclesData()
    {
        $id = 111;
        $query = [];
        $filters = [
            'page' => 1,
            'limit' => 10,
            'removalDate' => 'NULL'
        ];

        $mockLicenceVehicle = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        $mockLicenceVehicle->shouldReceive('getVehiclesDataForApplication')
            ->with(111, $filters)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getFilteredVehiclesData($id, $query));
    }

    public function providerFormatFilters()
    {
        return [
            'Defaults' => [
                [],
                [
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Pagination' => [
                [
                    'page' => 2,
                    'limit' => 25
                ],
                [
                    'page' => 2,
                    'limit' => 25,
                    'removalDate' => 'NULL'
                ]
            ],
            'Vrm all' => [
                [
                    'vrm' => 'All'
                ],
                [
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Vrm' => [
                [
                    'vrm' => 'A'
                ],
                [
                    'vrm' => '~A%',
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Specified all' => [
                [
                    'specified' => 'All'
                ],
                [
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Specified Y' => [
                [
                    'specified' => 'Y'
                ],
                [
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Specified N' => [
                [
                    'specified' => 'N'
                ],
                [
                    'specifiedDate' => 'NULL',
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Include removed' => [
                [
                    'includeRemoved' => '1'
                ],
                [
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NOT NULL'
                ]
            ],
            'Disc all' => [
                [
                    'disc' => 'All',
                ],
                [
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Disc Y' => [
                [
                    'disc' => 'Y',
                ],
                [
                    'disc' => 'Y',
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
            'Disc N' => [
                [
                    'disc' => 'N',
                ],
                [
                    'disc' => 'N',
                    'page' => 1,
                    'limit' => 10,
                    'removalDate' => 'NULL'
                ]
            ],
        ];
    }
}
