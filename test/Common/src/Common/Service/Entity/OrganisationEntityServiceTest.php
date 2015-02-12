<?php

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationEntityService;
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

    /**
     * @group entity_services
     */
    public function testGetLicences()
    {
        $id = 3;

        $orgData = [
            'licences' => 'LICENCES'
        ];

        $this->expectOneRestCall('Organisation', 'GET', $id)
            ->will($this->returnValue($orgData));

        $this->assertEquals('LICENCES', $this->sut->getLicences($id));
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
                            'children' => ['status'],
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
                    'apsts_granted'
                ]
            )
        );
    }
}
