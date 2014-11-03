<?php

/**
 * Workshop Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\WorkshopEntityService;

/**
 * Workshop Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class WorkshopEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new WorkshopEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetForLicence()
    {
        $id = 7;

        $data = array('licence' => $id);
        $expected = array('foo' => 'bar');
        $response = array('Results' => $expected);

        $this->expectOneRestCall('Workshop', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getForLicence($id));
    }

    /**
     * @group entity_services
     */
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('Workshop', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
