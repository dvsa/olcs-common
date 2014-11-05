<?php

/**
 * OrganisationPerson Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationPersonEntityService;

/**
 * OrganisationPerson Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationPersonEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OrganisationPersonEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     */
    public function testGetByOrgAndPersonIdWithNoRecords()
    {
        $orgId = 3;
        $personId = 7;

        $data = array(
            'Count' => 0
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', ['organisation' => $orgId, 'person' => $personId])
            ->will($this->returnValue($data));

        $this->sut->getByOrgAndPersonId($orgId, $personId);
    }

    /**
     * @group entity_services
     */
    public function testGetByOrgAndPersonId()
    {
        $orgId = 3;
        $personId = 7;

        $data = array(
            'Count' => 1,
            'Results' => array(
                'foo'
            )
        );

        $this->expectOneRestCall('OrganisationPerson', 'GET', ['organisation' => $orgId, 'person' => $personId])
            ->will($this->returnValue($data));

        $this->assertEquals('foo', $this->sut->getByOrgAndPersonId($orgId, $personId));
    }

    /**
     * @group entity_services
     */
    public function testDeleteByOrgAndPersonId()
    {
        $orgId = 3;
        $personId = 7;

        $this->expectOneRestCall('OrganisationPerson', 'DELETE', ['organisation' => $orgId, 'person' => $personId]);

        $this->sut->deleteByOrgAndPersonId($orgId, $personId);
    }

    /**
     * @group entity_services
     */
    public function testGetByPersonId()
    {
        $personId = 7;

        $this->expectOneRestCall('OrganisationPerson', 'GET', ['person' => $personId])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getByPersonId($personId));
    }
}
