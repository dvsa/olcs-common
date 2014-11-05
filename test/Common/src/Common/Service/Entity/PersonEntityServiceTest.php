<?php

/**
 * Person Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PersonEntityService;

/**
 * Person Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PersonEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PersonEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetAllForOrganisationWithNoLimit()
    {
        $id = 7;

        $data = array(
            'organisation' => $id,
            'limit' => 50
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAllForOrganisationWithLimit()
    {
        $id = 7;

        $data = array(
            'organisation' => $id,
            'limit' => 10
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllForOrganisation($id, 10));
    }

    /**
     * @group entity_services
     */
    public function testGetOneForOrganisationWithResults()
    {
        $id = 7;

        $data = array(
            'organisation' => $id,
            'limit' => 1
        );

        $restResult = array(
            'Count' => 1,
            'Results' => array(
                array(
                    'person' => array('foo' => 'bar')
                )
            )
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->willReturn($restResult);

        $this->assertEquals(array('foo' => 'bar'), $this->sut->getFirstForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOneForOrganisationWithNoResults()
    {
        $id = 7;

        $data = array(
            'organisation' => $id,
            'limit' => 1
        );

        $restResult = array(
            'Count' => 0,
            'Results' => array()
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->willReturn($restResult);

        $this->assertEquals(array(), $this->sut->getFirstForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('Person', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
