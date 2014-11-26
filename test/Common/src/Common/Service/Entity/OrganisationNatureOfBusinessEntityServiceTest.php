<?php

/**
 * OrganisationNatureOfBusiness Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OrganisationNatureOfBusinessEntityService;

/**
 * OrganisationNatureOfBusiness Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OrganisationNatureOfBusinessEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OrganisationNatureOfBusinessEntityService();

        parent::setUp();
    }

    /**
     * @group organisationNatureOfBusiness
     */
    public function testgetAllForOrganisation()
    {
        $id = 3;

        $this->expectOneRestCall('OrganisationNatureOfBusiness', 'GET', ['organisation' => $id, 'limit' => 'all'])
            ->will($this->returnValue(['Results' => []]));

        $this->assertEquals([], $this->sut->getAllForOrganisation($id));
    }

    /**
     * @group organisationNatureOfBusiness
     */
    public function testgetAllForOrganisationForSelect()
    {
        $id = 3;
        $returnValue = [
          'Results' => [[
              'refData' => [
                  'id' => 1
              ]
          ]]
        ];

        $this->expectOneRestCall('OrganisationNatureOfBusiness', 'GET', ['organisation' => $id, 'limit' => 'all'])
            ->will($this->returnValue($returnValue));

        $this->assertEquals([1], $this->sut->getAllForOrganisationForSelect($id));
    }

    /**
     * @group organisationNatureOfBusiness
     */
    public function testDeleteByOrganisationAndIds()
    {
        $id = 3;
        $ids = [1, 2, 3];
        $returnValue = [
          'Results' => [
            ['id' => 1], ['id' => 2], ['id' => 3]
          ]
        ];

        $this->expectedRestCallInOrder(
            'OrganisationNatureOfBusiness',
            'GET',
            ['organisation' => $id, 'refData' => 'IN ["1", "2", "3"]', 'limit' => 'all']
        )->will($this->returnValue($returnValue));

        $this->expectedRestCallInOrder('OrganisationNatureOfBusiness', 'DELETE', ['id' => 1]);
        $this->expectedRestCallInOrder('OrganisationNatureOfBusiness', 'DELETE', ['id' => 2]);
        $this->expectedRestCallInOrder('OrganisationNatureOfBusiness', 'DELETE', ['id' => 3]);

        $this->sut->deleteByOrganisationAndIds($id, $ids);
    }
}
