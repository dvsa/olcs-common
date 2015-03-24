<?php

/**
 * Transport Manager Application Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TransportManagerApplicationEntityService;

/**
 * Transport Manager Application Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerApplicationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected $dataBundle =[
        'children' => [
            'application' => [
                'children' => [
                    'status',
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'transportManager',
            'tmType',
            'operatingCentres'
        ]
    ];

    protected function setUp()
    {
        $this->sut = new TransportManagerApplicationEntityService();

        parent::setUp();
    }

    /**
     * Test get transport manager appplications
     *
     * @group transportManagerApplication
     */
    public function testGetTransportManagerApplications()
    {
        $id = 1;
        $returnValue = [
            'Results' => [
                [
                    'application' => [
                        'status' => [
                            'id' => 'apsts_consideration'
                        ]
                    ],
                    'operatingCentres' => [
                        'one',
                        'two'
                    ]
                ],
                [
                    'application' => [
                        'status' => [
                            'id' => 'foo'
                        ]
                    ],
                    'operatingCentres' => [
                        'one',
                        'two',
                        'three'
                    ]
                ],
            ]
        ];
        $status = [
            'apsts_consideration',
            'apsts_not_submitted',
            'apsts_granted'
        ];

        $expectedValue = [
            [
                'application' => [
                    'status' => [
                        'id' => 'apsts_consideration'
                    ]
                ],
                'operatingCentres' => [
                    'one',
                    'two'
                ],
                'ocCount' => 2
            ]
        ];

        $query = [
            'transportManager' => $id,
            'action' => '!=D',
            'limit' => 'all'
        ];

        $this->expectOneRestCall('TransportManagerApplication', 'GET', $query, $this->dataBundle)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($expectedValue, $this->sut->getTransportManagerApplications($id, $status));
    }

    /**
     * Test get transport manager appplication
     *
     * @group transportManagerApplication
     */
    public function testGetTransportManagerApplication()
    {
        $this->expectOneRestCall('TransportManagerApplication', 'GET', 1, $this->dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTransportManagerApplication(1));
    }

    public function testGetByApplication()
    {
        $id = 3;

        $this->expectOneRestCall('TransportManagerApplication', 'GET', ['application' => $id])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getByApplication($id));
    }

    public function testGetGrantDataForApplication()
    {
        $applicationId = 123;

        $query = ['application' => $applicationId, 'limit' => 'all'];

        $this->expectOneRestCall('TransportManagerApplication', 'GET', $query)
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getGrantDataForApplication($applicationId));
    }

    public function testDeleteForApplication()
    {
        $applicationId = 123;

        $query = ['application' => $applicationId];

        $this->expectOneRestCall('TransportManagerApplication', 'DELETE', $query)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->deleteForApplication($applicationId));
    }
}
