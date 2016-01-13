<?php

namespace CommonTest\Controller\Lva\Adapters;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceTransportManagerAdapter;
use Common\Service\Entity\LicenceEntityService;

/**
 * Variation Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceTransportManagerAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock('\Zend\ServiceManager\ServiceManager')->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock('\Zend\Mvc\Controller\AbstractController');

        $this->sut = new LicenceTransportManagerAdapter();
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setController($this->controller);
    }

    public function dataProviderTableData()
    {
        return [
            'existing' => [
                [
                    '99' => [
                        'id' => 'L12',
                        'name' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'status' => null,
                        'email' => 'bob@example.com',
                        'dob' => '2015-04-02',
                        'transportManager' => [
                            'id' => 99,
                            'homeCd' => [
                                'person' => [
                                    'name' => 'fred',
                                    'birthDate' => '2015-04-02'
                                ],
                                'emailAddress' => 'bob@example.com',
                            ]
                        ],
                        'action' => 'E',
                    ]
                ],
                [
                    'Results' => [
                        [
                            'id' => 12,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                        ],
                    ],
                ],
                ['Results' => []]
            ],

            // Updated
            'updated' => [
                [
                    '99a' => [
                        'id' => 412,
                        'name' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'status' => 'status',
                        'email' => 'bob@example.com',
                        'dob' => '2015-04-02',
                        'transportManager' => [
                            'id' => 99,
                            'homeCd' => [
                                'person' => [
                                    'name' => 'fred',
                                    'birthDate' => '2015-04-02'
                                ],
                                'emailAddress' => 'bob@example.com',
                            ]
                        ],
                        'action' => 'U',
                    ],
                    '99' => [
                        'id' => 'L12',
                        'name' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'status' => null,
                        'email' => 'bob@example.com',
                        'dob' => '2015-04-02',
                        'transportManager' => [
                            'id' => 99,
                            'homeCd' => [
                                'person' => [
                                    'name' => 'fred',
                                    'birthDate' => '2015-04-02'
                                ],
                                'emailAddress' => 'bob@example.com',
                            ]
                        ],
                        'action' => 'C',
                    ],
                ],
                [
                    'Results' => [
                        [
                            'id' => 12,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                        ],
                    ],
                ],
                [
                    'Results' => [
                        [
                            'id' => 412,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                            'tmApplicationStatus' => 'status',
                            'action' => 'U',
                        ],
                    ],
                ],
            ],

            // Deleted
            'deleted' => [
                [
                    '99a' => [
                        'id' => 412,
                        'name' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'status' => 'status',
                        'email' => 'bob@example.com',
                        'dob' => '2015-04-02',
                        'transportManager' => [
                            'id' => 99,
                            'homeCd' => [
                                'person' => [
                                    'name' => 'fred',
                                    'birthDate' => '2015-04-02'
                                ],
                                'emailAddress' => 'bob@example.com',
                            ]
                        ],
                        'action' => 'D',
                    ],
                ],
                [
                    'Results' => [
                        [
                            'id' => 12,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                        ],
                    ],
                ],
                [
                    'Results' => [
                        [
                            'id' => 412,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                            'tmApplicationStatus' => 'status',
                            'action' => 'D',
                        ],
                    ],
                ],
            ],

            // New
            'new' => [
                [
                    '99a' => [
                        'id' => 412,
                        'name' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'status' => 'status',
                        'email' => 'bob@example.com',
                        'dob' => '2015-04-02',
                        'transportManager' => [
                            'id' => 99,
                            'homeCd' => [
                                'person' => [
                                    'name' => 'fred',
                                    'birthDate' => '2015-04-02'
                                ],
                                'emailAddress' => 'bob@example.com',
                            ]
                        ],
                        'action' => 'A',
                    ],
                ],
                [
                    'Results' => [],
                ],
                [
                    'Results' => [
                        [
                            'id' => 412,
                            'transportManager' => [
                                'id' => 99,
                                'homeCd' => [
                                    'person' => [
                                        'name' => 'fred',
                                        'birthDate' => '2015-04-02'
                                    ],
                                    'emailAddress' => 'bob@example.com',
                                ]
                            ],
                            'tmApplicationStatus' => 'status',
                            'action' => 'A',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     */
    public function testGetTableData()
    {
        $this->markTestIncomplete();

        $tmlData = [
            'Results' => [
                [
                    'id' => 12,
                    'transportManager' => [
                        'id' => 99,
                        'homeCd' => [
                            'person' => [
                                'name' => 'fred',
                                'birthDate' => '2015-04-02'
                            ],
                            'emailAddress' => 'bob@example.com',
                        ]
                    ],
                ],
            ],
        ];

        $expectedData = [
            '99' => [
                'id' => 'L12',
                'name' => [
                    'name' => 'fred',
                    'birthDate' => '2015-04-02'
                ],
                'status' => null,
                'email' => 'bob@example.com',
                'dob' => '2015-04-02',
                'transportManager' => [
                    'id' => 99,
                    'homeCd' => [
                        'person' => [
                            'name' => 'fred',
                            'birthDate' => '2015-04-02'
                        ],
                        'emailAddress' => 'bob@example.com',
                    ]
                ],
            ],
        ];

        $mockTmlEntityService = m::mock('StdClass');
        $this->sm->setService('Entity\TransportManagerLicence', $mockTmlEntityService);

        $mockTmlEntityService->shouldReceive('getByLicenceWithHomeContactDetails')
            ->once()
            ->with(11)
            ->andReturn($tmlData);

        $this->assertEquals($expectedData, $this->sut->getTableData(44, 11));
    }

    public function testDelete()
    {
        // no op there no assertions
        $this->sut->delete([], 5);
    }
}
