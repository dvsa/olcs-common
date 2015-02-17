<?php

/**
 * Person Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PersonEntityService;
use CommonTest\Bootstrap;
use Mockery as m;

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

        $this->sm->setService(
            'Entity\OrganisationPerson',
            m::mock()
                ->shouldReceive('getAllByOrg')
                ->once()
                ->with($id, null)
                ->andReturn('RESPONSE')
                ->getMock()
        );

        $this->assertEquals('RESPONSE', $this->sut->getAllForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAllForOrganisationWithLimit()
    {
        $id = 7;
        $limit = 10;

        $this->sm->setService(
            'Entity\OrganisationPerson',
            m::mock()
                ->shouldReceive('getAllByOrg')
                ->once()
                ->with($id, $limit)
                ->andReturn('RESPONSE')
                ->getMock()
        );

        $this->assertEquals('RESPONSE', $this->sut->getAllForOrganisation($id, $limit));
    }

    /**
     * @group entity_services
     */
    public function testGetOneForOrganisationWithResults()
    {
        $id = 7;

        $orgPersonServiceResult = array(
            'Count' => 1,
            'Results' => array(
                array(
                    'person' => array('foo' => 'bar')
                )
            )
        );

        $this->sm->setService(
            'Entity\OrganisationPerson',
            m::mock()
                ->shouldReceive('getAllByOrg')
                ->once()
                ->with($id, 1)
                ->andReturn($orgPersonServiceResult)
                ->getMock()
        );

        $this->assertEquals(array('foo' => 'bar'), $this->sut->getFirstForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOneForOrganisationWithNoResults()
    {
        $id = 7;

        $orgPersonServiceResult = array(
            'Count' => 0,
            'Results' => array()
        );

        $this->sm->setService(
            'Entity\OrganisationPerson',
            m::mock()
                ->shouldReceive('getAllByOrg')
                ->once()
                ->with($id, 1)
                ->andReturn($orgPersonServiceResult)
                ->getMock()
        );

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
