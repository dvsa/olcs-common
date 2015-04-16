<?php

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Helper\DataHelperService;
use Mockery as m;

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OrganisationEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetApplications()
    {
        $id = 3;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getApplications($id));
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Organisation not found
     */
    public function testGetForUserWithNoUsers()
    {
        $id = 3;

        $data = array(
            'Count' => 0
        );

        $this->expectOneRestCall('OrganisationUser', 'GET', ['user' => $id])
            ->will($this->returnValue($data));

        $this->sut->getForUser($id);
    }

    /**
     * @group entity_services
     */
    public function testGetForUser()
    {
        $id = 3;

        $data = array(
            'Count' => 1,
            'Results' => array(
                array(
                    'organisation' => 'foo'
                )
            )
        );

        $this->expectOneRestCall('OrganisationUser', 'GET', ['user' => $id])
            ->will($this->returnValue($data));

        $this->assertEquals('foo', $this->sut->getForUser($id));
    }

    /**
     * @group entity_services
     */
    public function testGetType()
    {
        $id = 3;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getType($id));
    }

    /**
     * @group entity_services
     */
    public function testGetBusinessDetailsData()
    {
        $id = 3;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getBusinessDetailsData($id));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifier()
    {
        $this->expectOneRestCall('Organisation', 'GET', 123)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testHasInforceLicences()
    {
        $mock = m::mock()
            ->shouldReceive('getInForceForOrganisation')
            ->andReturn(
                [
                    'Count' => 5
                ]
            )
            ->getMock();

        $this->sm->setService('Entity\Licence', $mock);

        $this->assertTrue($this->sut->hasInForceLicences(123));
    }

    public function testChangedTradingNamesWithNoDiffs()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'tradingNames' => [
                [
                    'name' => 'foo'
                ],
                [
                    'name' => 'bar'
                ]
            ]
        ];

        $updated = [
            [
                'name' => 'foo'
            ],
            [
                'name' => 'bar'
            ]
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertFalse($this->sut->hasChangedTradingNames($id, $updated));
    }

    public function testChangedTradingNamesWithAdded()
    {
        $existing = [
            'tradingNames' => [
                [
                    'name' => 'foo'
                ]
            ]
        ];

        $updated = [
            [
                'name' => 'foo'
            ],
            [
                'name' => 'bar'
            ]
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedTradingNames($id, $updated));
    }

    public function testChangedTradingNamesWithRemoved()
    {
        $existing = [
            'tradingNames' => [
                [
                    'name' => 'foo'
                ],
                [
                    'name' => 'bar'
                ]
            ]
        ];

        $updated = [
            [
                'name' => 'bar'
            ]
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedTradingNames($id, $updated));
    }

    public function testChangedTradingNamesWithDifferentValues()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'tradingNames' => [
                [
                    'name' => 'foo'
                ],
                [
                    'name' => 'bar'
                ]
            ]
        ];

        $updated = [
            [
                'name' => 'foo'
            ],
            [
                'name' => 'baz'
            ]
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedTradingNames($id, $updated));
    }

    public function testHasChangedRegisteredAddressWithNoDiffs()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'contactDetails' => [
                'address' => [
                    'addressLine1' => 'one',
                    'addressLine2' => 'two',
                    'addressLine3' => 'three',
                    'addressLine4' => 'four',
                    'postcode' => 'LS20',
                    'town' => 'Leeds'
                ]
            ]
        ];

        $updated = [
            'addressLine1' => 'one',
            'addressLine2' => 'two',
            'addressLine3' => 'three',
            'addressLine4' => 'four',
            'postcode' => 'LS20',
            'town' => 'Leeds'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertFalse($this->sut->hasChangedRegisteredAddress($id, $updated));
    }

    public function testHasChangedRegisteredAddressWithDifferentData()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'contactDetails' => [
                'address' => [
                    'addressLine1' => 'one',
                    'addressLine2' => 'six',
                    'addressLine3' => 'three',
                    'addressLine4' => 'ten',
                    'postcode' => 'LS20',
                    'town' => 'Leeds'
                ]
            ]
        ];

        $updated = [
            'addressLine1' => 'one',
            'addressLine2' => 'two',
            'addressLine3' => 'three',
            'addressLine4' => 'four',
            'postcode' => 'LS20',
            'town' => 'Leeds'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedRegisteredAddress($id, $updated));
    }

    public function testChangedNatureOfBusinessWithNoDiffs()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'natureOfBusinesses' => [
                ['id' => 'foo'],
                ['id' => 'bar']
            ]
        ];

        $updated = [
            'foo', 'bar'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', ['id' => $id, 'limit' => 'all'])
            ->will($this->returnValue($existing));

        $this->assertFalse($this->sut->hasChangedNatureOfBusiness($id, $updated));
    }

    public function testChangedNatureOfBusinessWithDifferentValues()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'natureOfBusinesses' => [
                ['id' => 'foo'],
                ['id' => 'baz']
            ]
        ];

        $updated = [
            'foo', 'bar'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', ['id' => $id, 'limit' => 'all'])
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedNatureOfBusiness($id, $updated));
    }

    public function testChangedNatureOfBusinessWithAdded()
    {
        $existing = [
            'natureOfBusinesses' => [
                ['id' => 'foo'],
                ['id' => 'bar']
            ]
        ];

        $updated = [
            'foo', 'bar', 'baz'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', ['id' => $id, 'limit' => 'all'])
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedNatureOfBusiness($id, $updated));
    }

    public function testChangedNatureOfBusinessWithRemoved()
    {
        $existing = [
            'natureOfBusinesses' => [
                ['id' => 'foo'],
                ['id' => 'bar'],
                ['id' => 'baz']
            ]
        ];

        $updated = [
            'foo', 'bar'
        ];

        $id = 1;

        $this->expectOneRestCall('Organisation', 'GET', ['id' => $id, 'limit' => 'all'])
            ->will($this->returnValue($existing));

        $this->assertTrue($this->sut->hasChangedNatureOfBusiness($id, $updated));
    }

    public function testHasChangedSubsidiaryCompanyWithNoDiffs()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'companyNo' => '1234',
            'name' => 'foo'
        ];

        $updated = [
            'companyNo' => '1234',
            'name' => 'foo'
        ];

        $id = 123;

        $this->sm->setService(
            'Entity\CompanySubsidiary',
            m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn($existing)
            ->getMock()
        );

        $this->assertFalse($this->sut->hasChangedSubsidiaryCompany($id, $updated));
    }

    public function testHasChangedSubsidiaryCompanyWithDifferentData()
    {
        $this->sm->setService('Helper\Data', new DataHelperService());

        $existing = [
            'companyNo' => '1234',
            'name' => 'foo'
        ];

        $updated = [
            'companyNo' => '4212',
            'name' => 'foo'
        ];

        $id = 123;

        $this->sm->setService(
            'Entity\CompanySubsidiary',
            m::mock()
            ->shouldReceive('getById')
            ->with($id)
            ->andReturn($existing)
            ->getMock()
        );

        $this->assertTrue($this->sut->hasChangedSubsidiaryCompany($id, $updated));
    }

    /**
     * @group entity_services
     */
    public function testGetNewApplicationsByStatus()
    {
        $orgData = [
            'licences' => [
                [
                    'id' => 7,
                    'applications' => [
                        ['id' => 20],
                        ['id' => 21],
                    ],
                ],
                [
                    'id' => 8,
                    'applications' => [
                        ['id' => 22],
                        ['id' => 23],
                    ],
                ],
            ],
        ];

        $expectedBundle = [
            'children' => [
                'licences' => [
                    'children' => [
                        'applications' => [
                            'children' => ['status', 'licenceType', 'goodsOrPsv'],
                            'criteria' => [
                                'status' => 'IN ["apsts_consideration","apsts_granted"]',
                                'isVariation' => false,
                            ],
                        ],
                        'licenceType',
                        'status',
                    ],
                ],
            ],
        ];
        $this->expectOneRestCall('Organisation', 'GET', 123, $expectedBundle)
            ->will($this->returnValue($orgData));

        $expectedResult = [
            ['id' => 20],
            ['id' => 21],
            ['id' => 22],
            ['id' => 23],
        ];
        $this->assertEquals(
            $expectedResult,
            $this->sut->getNewApplicationsByStatus(
                123,
                [
                    'apsts_consideration',
                    'apsts_granted',
                ]
            )
        );
    }


    /**
     * @group entity_services
     */
    public function testGetAllApplicationsByStatus()
    {
        $orgData = [
            'licences' => [
                [
                    'id' => 7,
                    'applications' => [
                        ['id' => 20],
                        ['id' => 21],
                    ],
                ],
                [
                    'id' => 8,
                    'applications' => [
                        ['id' => 22],
                        ['id' => 23],
                    ],
                ],
            ],
        ];

        $expectedBundle = [
            'children' => [
                'licences' => [
                    'children' => [
                        'applications' => [
                            'children' => ['status', 'licenceType', 'goodsOrPsv'],
                            'criteria' => [
                                'status' => 'IN ["apsts_consideration","apsts_granted"]',
                            ],
                        ],
                        'licenceType',
                        'status',
                    ],
                ],
            ],
        ];
        $this->expectOneRestCall('Organisation', 'GET', 123, $expectedBundle)
            ->will($this->returnValue($orgData));

        $expectedResult = [
            ['id' => 20],
            ['id' => 21],
            ['id' => 22],
            ['id' => 23],
        ];
        $this->assertEquals(
            $expectedResult,
            $this->sut->getAllApplicationsByStatus(
                123,
                [
                    'apsts_consideration',
                    'apsts_granted',
                ]
            )
        );
    }

    /**
     * @group entity_services
     */
    public function testGetLicencesByStatus()
    {
        $orgData = [
            'licences' => 'LICENCES'
        ];

        $expectedBundle = [
            'children' => [
                'licences' => [
                    'children' => [
                        'licenceType',
                        'status',
                        'goodsOrPsv',
                    ],
                    'criteria' => [
                        'status' => 'IN ["lsts_valid","lsts_suspended","lsts_curtailed"]'
                    ],
                ],
            ],
        ];
        $this->expectOneRestCall('Organisation', 'GET', 123, $expectedBundle)
            ->will($this->returnValue($orgData));

        $this->assertEquals(
            'LICENCES',
            $this->sut->getLicencesByStatus(
                123,
                [
                    'lsts_valid',
                    'lsts_suspended',
                    'lsts_curtailed',
                ]
            )
        );
    }

    public function testGetRegisteredUsersForSelect()
    {
        $id = 111;
        $data = [
            'organisationUsers' => [
                [
                    'user' => [
                        'id' => 11,
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Charles',
                                'familyName' => 'Darwin'
                            ]
                        ]
                    ]
                ],
                [
                    'user' => [
                        'id' => 22,
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Alan',
                                'familyName' => 'Carr'
                            ]
                        ]
                    ]
                ],
                [
                    'user' => [
                        'id' => 33,
                        'contactDetails' => [
                            'person' => [
                                'forename' => 'Elvis',
                                'familyName' => 'Presley'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $expected = [
            22 => 'Alan Carr',
            11 => 'Charles Darwin',
            33 => 'Elvis Presley'
        ];

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertEquals($expected, $this->sut->getRegisteredUsersForSelect($id));
    }
}
