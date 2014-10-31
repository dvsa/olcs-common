<?php

/**
 * Organisation Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationEntityService;

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
}
