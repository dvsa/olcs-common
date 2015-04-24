<?php

/**
 * Inspection Request Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\InspectionRequestEntityService;

/**
 * Inspection Request Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class InspectionRequestEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new InspectionRequestEntityService();

        parent::setUp();
    }

    /**
     * @group inspectionRequestEntityService
     */
    public function testGetInspectionRequestList()
    {
        $query = [
            'foo' => 'bar',
            'limit' => 'all'
        ];

        $bundle = [
            'children' => [
                'reportType',
                'resultType',
                'application',
                'licence' => [
                    'criteria' => [
                        'id' => 1,
                    ],
                    'required' => true,
                ],
            ]
        ];

        $this->expectOneRestCall('InspectionRequest', 'GET', $query, $bundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getInspectionRequestList($query, 1));
    }

    /**
     * @group inspectionRequestEntityService
     */
    public function testGetInspectionRequest()
    {
        $expectedBundle = [
            'children' => [
                'reportType',
                'requestType',
                'resultType',
                'application' => [
                    'children' => [
                        'licenceType',
                        'operatingCentres' => [
                            'children' => [
                                'operatingCentre' => [
                                    'children' => [
                                        'address'
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'licence' => [
                    'children' => [
                        'enforcementArea',
                        'licenceType',
                        'organisation' => [
                            'children' => [
                                'tradingNames',
                                'licences',
                            ],
                        ],
                        'operatingCentres',
                        'correspondenceCd' => [
                            'children' => [
                                'address' => [],
                                'phoneContacts' => [
                                    'children' => [
                                        'phoneContactType',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'operatingCentre' => [
                    'children' => [
                        'address'
                    ],
                ],
            ],
        ];

        $this->expectOneRestCall('InspectionRequest', 'GET', 1, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getInspectionRequest(1));
    }
}
