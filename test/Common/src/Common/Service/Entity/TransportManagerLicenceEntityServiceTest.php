<?php

/**
 * Transport Manager Licence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TransportManagerLicenceEntityService;

/**
 * Transport Manager Licence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerLicenceEntityServiceTest extends AbstractEntityServiceTestCase
{

    protected $dataBundle = [
        'children' => [
            'licence' => [
                'children' => [
                    'organisation',
                    'status'
                ]
            ],
            'transportManager' => [
                'children' => [
                    'tmType'
                ]
            ],
            'tmType',
            'operatingCentres'
        ]
    ];

    protected function setUp()
    {
        $this->sut = new TransportManagerLicenceEntityService();

        parent::setUp();
    }

    /**
     * @group transportManagerLicences
     */
    public function testGetTransportManagerLicences()
    {
        $id = 1;
        $returnValue = [
            'Results' => [
                [
                    'licence' => [
                        'status' => [
                            'id' => 'lsts_dummy'
                        ]
                    ],
                    'operatingCentres' => [
                        'one',
                        'two'
                    ]
                ],
                [
                    'licence' => [
                        'status' => [
                            'id' => 'lsts_valid'
                        ]
                    ],
                    'operatingCentres' => [
                        'one',
                        'two'
                    ]
                ],
                [
                    'licence' => [
                        'status' => [
                            'id' => 'lsts_suspended'
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
            'lsts_valid',
            'lsts_suspended',
            'lsts_curtailed'
        ];

        $expectedValue = [
            [
                'licence' => [
                    'status' => [
                        'id' => 'lsts_valid'
                    ]
                ],
                'operatingCentres' => [
                    'one',
                    'two'
                ],
                'ocCount' => 2
            ],
            [
                'licence' => [
                    'status' => [
                        'id' => 'lsts_suspended'
                    ]
                ],
                'operatingCentres' => [
                    'one',
                    'two',
                    'three'
                ],
                'ocCount' => 3
            ],
        ];

        $this->expectOneRestCall('TransportManagerLicence', 'GET', ['transportManager' => $id])
            ->will($this->returnValue($returnValue));

        $this->assertEquals($expectedValue, $this->sut->getTransportManagerLicences($id, $status));
    }

    /**
     * Test get transport manager licence
     *
     * @group transportManagerLicences
     */
    public function testGetTransportManagerLicence()
    {
        $this->expectOneRestCall('TransportManagerLicence', 'GET', 1, $this->dataBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTransportManagerLicence(1));
    }

    public function testGetByTransportManagerAndLicence()
    {
        $tmId = 123;
        $licenceId = 321;

        $query = [
            'transportManager' => $tmId,
            'licence' => $licenceId
        ];

        $this->expectOneRestCall('TransportManagerLicence', 'GET', $query)
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getByTransportManagerAndLicence($tmId, $licenceId));
    }
}
