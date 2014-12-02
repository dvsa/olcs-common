<?php

/**
 * TransportManager Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\TransportManagerEntityService;

/**
 * TransportManager Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManagerEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new TransportManagerEntityService();

        parent::setUp();
    }

    /**
     * @group transportManagerEntity
     */
    public function testGetTmDetails()
    {

        $bundle = [
            'properties' => [
                'version',
            ],
            'children' => [
                'contactDetails' => [
                    'properties' => [
                        'id',
                        'version',
                        'emailAddress'
                    ],
                    'children' => [
                        'person' => [
                            'properties' => [
                                'id',
                                'version',
                                'forename',
                                'familyName',
                                'title',
                                'birthDate',
                                'birthPlace'
                            ]
                        ],
                        'address' => [
                            'properties' => [
                                'id',
                                'version',
                                'addressLine1',
                                'addressLine2',
                                'addressLine3',
                                'addressLine4',
                                'town',
                                'postcode'
                            ]
                        ],
                        'contactType' => [
                            'properties' => [
                                'id'
                            ]
                        ]
                    ]
                ],
                'tmType' => [
                    'properties' => [
                        'id'
                    ]
                ],
                'tmStatus' => [
                    'properties' => [
                        'id'
                    ]
                ],
            ]
        ];

        $this->expectOneRestCall('TransportManager', 'GET', 1, $bundle)
            ->will($this->returnValue([]));

        $this->assertEquals([], $this->sut->getTmDetails(1));
    }
}
