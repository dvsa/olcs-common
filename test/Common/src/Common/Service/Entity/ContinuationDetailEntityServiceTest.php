<?php

/**
 * Continuation Detail Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ContinuationDetailEntityService;

/**
 * Continuation Detail Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ContinuationDetailEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ContinuationDetailEntityService();

        parent::setUp();
    }

    public function testCreateRecords()
    {
        $records = [
            ['foo' => 'bar']
        ];

        $data = [
            ['foo' => 'bar'],
            '_OPTIONS_' => [
                'multiple' => true
            ]
        ];

        $this->expectOneRestCall('ContinuationDetail', 'POST', $data);

        $this->sut->createRecords($records);
    }

    /**
     * @dataProvider providerGetListData
     */
    public function testGetListData($continuationId, $filters, $expectedData, $expectedBundle)
    {
        $this->expectOneRestCall('ContinuationDetail', 'GET', $expectedData, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getListData($continuationId, $filters));
    }

    public function providerGetListData()
    {
        return [
            'No filters' => [
                111,
                [],
                [
                    'continuation' => 111,
                    'limit' => 'all'
                ],
                [
                    'children' => [
                        'status',
                        'licence' => [
                            'required' => true,
                            'sort' => 'licNo',
                            'order' => 'ASC',
                            'children' => [
                                'status',
                                'organisation' => [
                                    'required' => true
                                ],
                                'licenceType',
                                'goodsOrPsv',
                            ]
                        ]
                    ]
                ]
            ],
            'Licence filters' => [
                111,
                [
                    'licenceNo' => 'OB123',
                    'licenceStatus' => ['foo', 'bar']
                ],
                [
                    'continuation' => 111,
                    'limit' => 'all'
                ],
                [
                    'children' => [
                        'status',
                        'licence' => [
                            'required' => true,
                            'sort' => 'licNo',
                            'order' => 'ASC',
                            'criteria' => [
                                'licNo' => 'OB123',
                                'status' => ['foo', 'bar']
                            ],
                            'children' => [
                                'status',
                                'organisation' => [
                                    'required' => true
                                ],
                                'licenceType',
                                'goodsOrPsv',
                            ]
                        ]
                    ]
                ]
            ],
            'Org filters post' => [
                111,
                [
                    'method' => 'post'
                ],
                [
                    'continuation' => 111,
                    'limit' => 'all'
                ],
                [
                    'children' => [
                        'status',
                        'licence' => [
                            'required' => true,
                            'sort' => 'licNo',
                            'order' => 'ASC',
                            'children' => [
                                'status',
                                'organisation' => [
                                    'required' => true,
                                    'criteria' => [
                                        'allowEmail' => 0
                                    ],
                                ],
                                'licenceType',
                                'goodsOrPsv',
                            ]
                        ]
                    ]
                ]
            ],
            'Org filters email' => [
                111,
                [
                    'method' => 'email'
                ],
                [
                    'continuation' => 111,
                    'limit' => 'all'
                ],
                [
                    'children' => [
                        'status',
                        'licence' => [
                            'required' => true,
                            'sort' => 'licNo',
                            'order' => 'ASC',
                            'children' => [
                                'status',
                                'organisation' => [
                                    'required' => true,
                                    'criteria' => [
                                        'allowEmail' => 1
                                    ],
                                ],
                                'licenceType',
                                'goodsOrPsv',
                            ]
                        ]
                    ]
                ]
            ],
            'Status filter' => [
                111,
                [
                    'status' => 'foo'
                ],
                [
                    'status' => 'foo',
                    'continuation' => 111,
                    'limit' => 'all'
                ],
                [
                    'children' => [
                        'status',
                        'licence' => [
                            'required' => true,
                            'sort' => 'licNo',
                            'order' => 'ASC',
                            'children' => [
                                'status',
                                'organisation' => [
                                    'required' => true
                                ],
                                'licenceType',
                                'goodsOrPsv',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function testGetContinuationMarker()
    {
        $mockDateHelper = \Mockery::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $mockDateHelper->shouldReceive('getDateObject')->with()->once()->andReturn(new \DateTime('2015-05-01'));

        $query = [
            'licence' => 1966,
            [
                [
                    'status' => [
                        ContinuationDetailEntityService::STATUS_PRINTED,
                        ContinuationDetailEntityService::STATUS_ACCEPTABLE,
                        ContinuationDetailEntityService::STATUS_UNACCEPTABLE
                    ],
                ],
                [
                    'status' => ContinuationDetailEntityService::STATUS_COMPLETE,
                    'received' => 0
                ]
            ],
           'limit' => 'all'
        ];

        $bundle = [
            'children' => [
                'status',
                'licence' => [
                    'children' => ['status'],
                    'criteria' => [
                        'status' => [
                            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_VALID,
                            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_CURTAILED,
                            \Common\Service\Entity\LicenceEntityService::LICENCE_STATUS_SUSPENDED,
                        ]
                    ],
                    'required' => true,
                ],
                'continuation' => [
                    'criteria' => [
                        [
                            [
                                'year' => "2015",
                                'month' => ">= 5"
                            ],
                            // or
                            [
                                'year' => [
                                    [
                                        "> 2015",
                                        "< 2019"
                                    ]
                                ]
                            ],
                            // or
                            [
                                'year' => "2019",
                                'month' => "< 5"
                            ]
                        ]
                    ],
                    'required' => true,
                ]
            ]
        ];

        $this->expectOneRestCall('ContinuationDetail', 'GET', $query, $bundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getContinuationMarker(1966));
    }

    public function testGetOngoingForLicence()
    {
        $expectedQuery = [
            'licence' => 1966,
            'status' => ContinuationDetailEntityService::STATUS_ACCEPTABLE,
            'limit' => 'all',

        ];
        $expectedBundle = [
            'children' => [
                'licence' => [
                    'children' => [
                        'status',
                    ]
                ],
            ]
        ];

        $this->expectOneRestCall('ContinuationDetail', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue(['Count' => 1, 'Results' => ['STUFF']]));

        $this->assertEquals('STUFF', $this->sut->getOngoingForLicence(1966));
    }

    public function testGetOngoingForLicenceNoResults()
    {
        $expectedQuery = [
            'licence' => 1966,
            'status' => ContinuationDetailEntityService::STATUS_ACCEPTABLE,
            'limit' => 'all',

        ];
        $expectedBundle = [
            'children' => [
                'licence' => [
                    'children' => [
                        'status',
                    ]
                ],
            ]
        ];

        $this->expectOneRestCall('ContinuationDetail', 'GET', $expectedQuery, $expectedBundle)
            ->will($this->returnValue(['Count' => 0, 'Results' => []]));

        $this->assertFalse($this->sut->getOngoingForLicence(1966));
    }

    public function testProcessContinuationDetail()
    {
        $query = ['id' => 1, 'docId' => 2];
        $this->expectOneRestCall('ContinuationDetail/Checklists', 'PUT', $query)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->processContinuationDetail(1, 2));
    }
}
