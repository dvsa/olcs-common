<?php

namespace CommonTest\Controller\Lva\Adapters;

use Common\Controller\Lva\Adapters\VariationTransportManagerAdapter;
use Common\Service\Cqrs\Command\CommandService;
use Common\Service\Cqrs\Query\CachingQueryService;
use Dvsa\Olcs\Transfer\Util\Annotation\AnnotationBuilder as TransferAnnotationBuilder;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Variation Transport Manager Adapter Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class VariationTransportManagerAdapterTest extends MockeryTestCase
{
    /** @var  VariationTransportManagerAdapter */
    protected $sut;
    /** @var \Zend\ServiceManager\ServiceManager|\Mockery\MockInterface */
    protected $sm;
    /** @var  \Zend\Mvc\Controller\AbstractController */
    protected $controller;

    public function setUp()
    {
        $this->sm = m::mock(\Zend\ServiceManager\ServiceManager::class)->makePartial();
        $this->sm->setAllowOverride(true);

        $this->controller = m::mock(\Zend\Mvc\Controller\AbstractController::class);

        /** @var TransferAnnotationBuilder $mockAnnotationBuilder */
        $mockAnnotationBuilder = m::mock(TransferAnnotationBuilder::class);
        /** @var CachingQueryService $mockQuerySrv */
        $mockQuerySrv = m::mock(CachingQueryService::class);
        /** @var CommandService $mockCommandSrv */
        $mockCommandSrv = m::mock(CommandService::class);

        $this->sut = new VariationTransportManagerAdapter(
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
     * @dataProvider dataProviderTableData
     */
    public function testGetTableData($expectedData, $tmlData, $tmaData)
    {
        $this->markTestIncomplete();

        $mockTmaEntityService = m::mock('StdClass');
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmaEntityService);

        $mockTmlEntityService = m::mock('StdClass');
        $this->sm->setService('Entity\TransportManagerLicence', $mockTmlEntityService);

        $mockTmlEntityService->shouldReceive('getByLicenceWithHomeContactDetails')
            ->once()
            ->with(11)
            ->andReturn($tmlData);

        $mockTmaEntityService->shouldReceive('getByApplicationWithHomeContactDetails')
            ->once()
            ->with(44)
            ->andReturn($tmaData);

        $this->assertEquals($expectedData, $this->sut->getTableData(44, 11));
    }

    public function testDelete()
    {
        $this->markTestIncomplete();

        $mockResponse = m::mock();
        $mockResponse->shouldReceive('isOk')->andReturn(true);

        $mockDeltaDeleteTransportManagerLicence = m::mock();
        $this->sm->shouldReceive('get->get')
            ->once()
            ->with('Lva\DeltaDeleteTransportManagerLicence')
            ->andReturn($mockDeltaDeleteTransportManagerLicence);

        $mockDeltaDeleteTransportManagerLicence->shouldReceive('process')
            ->once()
            ->with(['transportManagerLicenceId' => 5, 'applicationId' => 66])
            ->andReturn($mockResponse);

        $mockDeleteTransportManagerApplication = m::mock();
        $this->sm->shouldReceive('get->get')
            ->once()
            ->with('Lva\DeleteTransportManagerApplication')
            ->andReturn($mockDeleteTransportManagerApplication);

        $mockDeleteTransportManagerApplication->shouldReceive('process')
            ->once()
            ->with(['ids' => [3]]);

        $this->sut->delete([3, 'L5'], 66);
    }
}
