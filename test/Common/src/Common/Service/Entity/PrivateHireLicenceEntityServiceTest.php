<?php

/**
 * PrivateHireLicence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\PrivateHireLicenceEntityService;

/**
 * PrivateHireLicence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PrivateHireLicenceEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new PrivateHireLicenceEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetByLicenceId()
    {
        $id = 7;

        $data = array('licence' => $id);
        $expected = array('foo');
        $response = array('Results' => $expected);

        $this->expectOneRestCall('PrivateHireLicence', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getByLicenceId($id));
    }

    /**
     * @group entity_services
     */
    public function testGetCountByLicence()
    {
        $id = 7;

        $data = array('licence' => $id);
        $response = array('Count' => 3);

        $this->expectOneRestCall('PrivateHireLicence', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals(3, $this->sut->getCountByLicence($id));
    }

    /**
     * @group entity_services
     */
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('PrivateHireLicence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }
}
