<?php

/**
 * LicenceVehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
use Common\Service\Entity\LicenceVehicleEntityService;

/**
 * LicenceVehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicleEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new LicenceVehicleEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetVehicle()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVehicle($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclePsv()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVehiclePsv($id));
    }

    /**
     * @group entity_services
     */
    public function testGetActiveDiscs()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getActiveDiscs($id));
    }

    /**
     * @group entity_services
     */
    public function testDelete()
    {
        $id = 3;

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = array(
            'id' => $id,
            'removalDate' => $date,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $this->expectOneRestCall('LicenceVehicle', 'PUT', $data);

        $this->sut->delete($id);
    }

    /**
     * @group entity_services
     */
    public function testGetDiscPendingData()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDiscPendingData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVrm()
    {
        $id = 3;

        $data = array(
            'vehicle' => array(
                'vrm' => 'foo'
            )
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertEquals('foo', $this->sut->getVrm($id));
    }

    /**
     * @group entity_services
     */
    public function testGetCurrentVrmsForLicence()
    {
        $id = 3;

        $data = array(
            'Count' => 3,
            'Results' => array(
                array(
                    'vehicle' => array(
                        'vrm' => 'foo'
                    )
                ),
                array(
                    'vehicle' => array(
                        'vrm' => 'bar'
                    )
                ),
                array(
                    'vehicle' => array(
                        'vrm' => 'cake'
                    )
                ),
            )
        );

        $params = array(
            'licence' => $id,
            'removalDate' => 'NULL',
            'limit' => 'all'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $params)
            ->will($this->returnValue($data));

        $this->assertEquals(array('foo', 'bar', 'cake'), $this->sut->getCurrentVrmsForLicence($id));
    }

    /**
     * @group entity_services
     */
    public function testGetForApplicationValidation()
    {
        $id = 3;
        $applicationId = 8;

        $query = array(
            'licence' => $id,
            'removalDate' => 'NULL',
            'limit' => 'all',
            'application' => $applicationId
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getForApplicationValidation($id, $applicationId));
    }

    /**
     * @group entity_services
     */
    public function testExistingForLicence()
    {
        $id = 3;
        $applicationId = 8;

        $query = array(
            'licence' => $id,
            'specifiedDate' => 'NOT NULL',
            'removalDate' => 'NULL',
            'interimApplication' => 'NULL',
            'application' => '!= ' . $applicationId,
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getExistingForLicence($id, $applicationId));
    }

    /**
     * @group entity_services
     */
    public function testGetExistingForApplication()
    {
        $applicationId = 69;

        $query = array(
            'removalDate' => 'NULL',
            'application' => $applicationId,
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getExistingForApplication($applicationId));
    }

    /**
     * @group entity_services
     */
    public function testRemoveVehicles()
    {
        $ids = [1, 2];
        $date = '2015-03-24';

        $this->mockDate($date);

        $expectedData = [
            0 => [
                'id' => 1,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            1 => [
                'id' => 2,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            '_OPTIONS_' => ['multiple' => true],
        ];

        $this->expectOneRestCall('LicenceVehicle', 'PUT', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->removeVehicles($ids));
    }

    /**
     * @group entity_services
     */
    public function testRemoveForApplication()
    {

        $applicationId = 69;
        $date = '2015-03-24';

        $this->mockDate($date);

        $query = array(
            'removalDate' => 'NULL',
            'application' => $applicationId,
            'limit' => 'all'
        );
        $response = array(
            'Results' => [
                ['id' => 1],
                ['id' => 2],
            ]
        );

        $this->expectedRestCallInOrder('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $expectedData = [
            0 => [
                'id' => 1,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            1 => [
                'id' => 2,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            '_OPTIONS_' => ['multiple' => true],
        ];

        $this->expectedRestCallInOrder('LicenceVehicle', 'PUT', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->removeForApplication($applicationId));
    }

    /**
     * @dataProvider providerGetVehiclesDataForApplication
     */
    public function testGetVehiclesDataForApplication($filters, $expectedQuery, $expectedBundle)
    {
        $applicationId = 111;

        // Mocks
        $mockApplication = m::mock();
        $this->sm->setService('Entity\Application', $mockApplication);
        $mockApplication->shouldReceive('getLicenceIdForApplication')
            ->with(111)
            ->andReturn(222);

        $this->expectOneRestCall('LicenceVehicle', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->sut->getVehiclesDataForApplication($applicationId, $filters);
    }

    /**
     * @dataProvider providerGetVehiclesDataForLicence
     */
    public function testGetVehiclesDataForLicence($filters, $expectedQuery, $expectedBundle)
    {
        $licenceId = 222;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->sut->getVehiclesDataForLicence($licenceId, $filters);
    }

    public function providerGetVehiclesDataForApplication()
    {
        return [
            'no filters' => [
                [],
                [
                    'application' => 111,
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'no filters, pagination' => [
                [
                    'page' => 2,
                    'limit' => 25
                ],
                [
                    'application' => 111,
                    'page' => 2,
                    'limit' => 25
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with null specified date' => [
                [
                    'specifiedDate' => 'NULL'
                ],
                [
                    'application' => 111,
                    'specifiedDate' => 'NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with not null specified date' => [
                [
                    'specifiedDate' => 'NOT NULL'
                ],
                [
                    [
                        'application' => 111,
                        'licence' => 222
                    ],
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with removal date' => [
                [
                    'removalDate' => 'NOT NULL'
                ],
                [
                    'application' => 111,
                    'removalDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with vrm' => [
                [
                    'vrm' => '~A%'
                ],
                [
                    'application' => 111,
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [
                            'criteria' => [
                                'vrm' => '~A%',
                            ],
                            'required' => true,
                        ]
                    ]
                ]
            ],
            'with disc Y' => [
                [
                    'disc' => 'Y'
                ],
                [
                    'application' => 111,
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [
                            'required' => true,
                            'criteria' => [
                                'ceasedDate' => 'NULL',
                                'issuedDate' => 'NOT NULL'
                            ]
                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with disc N' => [
                [
                    'disc' => 'N'
                ],
                [
                    'application' => 111,
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [
                            'requireNone' => true,
                            'criteria' => [
                                'ceasedDate' => 'NULL',
                                'issuedDate' => 'NOT NULL'
                            ]
                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
        ];
    }

    public function providerGetVehiclesDataForLicence()
    {
        return [
            'no filters' => [
                [],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'no filters, pagination' => [
                [
                    'page' => 2,
                    'limit' => 25
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 2,
                    'limit' => 25
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with null specified date' => [
                [
                    'specifiedDate' => 'NULL'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with not null specified date' => [
                [
                    'specifiedDate' => 'NOT NULL'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with removal date' => [
                [
                    'removalDate' => 'NOT NULL'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'removalDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with vrm' => [
                [
                    'vrm' => '~A%'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [

                        ],
                        'interimApplication',
                        'vehicle' => [
                            'criteria' => [
                                'vrm' => '~A%',
                            ],
                            'required' => true,
                        ]
                    ]
                ]
            ],
            'with disc Y' => [
                [
                    'disc' => 'Y'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [
                            'required' => true,
                            'criteria' => [
                                'ceasedDate' => 'NULL',
                                'issuedDate' => 'NOT NULL'
                            ]
                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
            'with disc N' => [
                [
                    'disc' => 'N'
                ],
                [
                    'licence' => 222,
                    'specifiedDate' => 'NOT NULL',
                    'page' => 1,
                    'limit' => 10
                ],
                [
                    'children' => [
                        'goodsDiscs' => [
                            'requireNone' => true,
                            'criteria' => [
                                'ceasedDate' => 'NULL',
                                'issuedDate' => 'NOT NULL'
                            ]
                        ],
                        'interimApplication',
                        'vehicle' => [

                        ]
                    ]
                ]
            ],
        ];
    }
}
