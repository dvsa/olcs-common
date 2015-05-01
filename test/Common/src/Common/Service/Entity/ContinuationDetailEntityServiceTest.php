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
}
