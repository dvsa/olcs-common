<?php

/**
 * Company Subsidiary Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\CompanySubsidiaryEntityService;

/**
 * Company Subsidiary Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CompanySubsidiaryEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new CompanySubsidiaryEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetAllForOrganisation()
    {
        $id = 3;

        $this->expectOneRestCall('CompanySubsidiary', 'GET', ['organisation' => $id])
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAllForOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testGetById()
    {
        $id = 3;

        $this->expectOneRestCall('CompanySubsidiary', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
