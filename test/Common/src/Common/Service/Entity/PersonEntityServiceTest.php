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
    public function testGetAllForOrganisation()
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
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('Person', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
