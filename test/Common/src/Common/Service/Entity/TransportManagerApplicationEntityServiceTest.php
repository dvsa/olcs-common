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
                    'licence' => [
                        'children' => [
                            'organisation'
                        ]
                    ]
                ]
            ],
            'tmApplicationStatus',
            'transportManager',
            'tmType',
            'tmApplicationOcs' => [
                'children' => [
                    'operatingCentre'
                ]
            ]
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
        $bundle = [
            'children' => [
                'application' => [
                    'children' => [
                        'licence' => [
                            'children' => [
                                'organisation'
                            ]
                        ]
                    ]
                ],
                'tmApplicationStatus',
                'transportManager',
                'tmType',
                'tmApplicationOcs' => [
                    'children' => [
                        'operatingCentre'
                    ]
                ]
            ]
        ];
        $returnValue = [
            'Results' => [
                [
                    'tmApplicationOcs' => [
                        'one',
                        'two'
                    ]
                ],
                [
                    'tmApplicationOcs' => [
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
            'Results' => [
                [
                    'tmApplicationOcs' => [
                        'one',
                        'two'
                    ],
                    'ocCount' => 2
                ],
                [
                    'tmApplicationOcs' => [
                        'one',
                        'two',
                        'three'
                    ],
                    'ocCount' => 3
                ],
            ]
        ];

        $query = [
            'transportManagerId' => $id,
            'action' => '!= D',
            'tmApplicationStatus' => [
                'apsts_consideration',
                'apsts_not_submitted',
                'apsts_granted'
            ],
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
}
