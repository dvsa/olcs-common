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
    public function testGetAllByOrgWithNoLimit()
    {
        $id = 7;

        $data = [
            'organisation' => $id,
            'limit' => 50
        ];

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllByOrg($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAllByOrgWithLimit()
    {
        $id = 7;
        $limit = 10;

        $data = [
            'organisation' => $id,
            'limit' => $limit,
        ];

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllByOrg($id, $limit));
    }

    /**
     * @group entity_services
     */
    public function testGetAllWithPerson()
    {
        $id = 7;

        $data = [
            'person' => $id
        ];

        $this->expectOneRestCall('OrganisationPerson', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllWithPerson($id));
    }
}
