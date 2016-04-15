<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\LicenceTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\Mvc\Controller\AbstractController;

/**
 * Variation Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var  LicenceTransportManagerAdapter */
    protected $sut;
    /** @var \Zend\ServiceManager\ServiceManager|\Mockery\MockInterface */
    protected $sm;
    /** @var  AbstractController */
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock(\Zend\ServiceManager\ServiceManager::class)->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock(AbstractController::class);

        /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
        $mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        /** @var CachingQueryService $mockQuerySrv */
        $mockQuerySrv = m::mock(CachingQueryService::class);
        /** @var CommandService $mockCommandSrv */
        $mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new LicenceTransportManagerAdapter(
            $mockAnnotationBuilder, $mockQuerySrv, $mockCommandSrv
        );
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
