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
    protected function setUp()
    {
        $this->sut = new TransportManagerApplicationEntityService();

        parent::setUp();
    }

    /**
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
                                'organisation' => [
                                    'properties' => ['id', 'name']
                                ]
                            ]
                        ]
                    ]
                ],
                'tmApplicationStatus',
                'transportManager',
                'tmType',
                'tmApplicationOcs'
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
            'action' => '!= :act',
            'action' => 'NULL',
            'tmApplicationStatus' => [
                'apsts_consideration',
                'apsts_not_submitted',
                'apsts_granted'
            ],
        ];
        $this->expectOneRestCall('TransportManagerApplication', 'GET', $query, $bundle)
            ->will($this->returnValue($returnValue));

        $this->assertEquals($expectedValue, $this->sut->getTransportManagerApplications($id, $status));
    }
}
