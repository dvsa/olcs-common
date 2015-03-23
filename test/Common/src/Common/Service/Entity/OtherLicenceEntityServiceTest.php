<?php

/**
 * OtherLicence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\OtherLicenceEntityService;

/**
 * OtherLicence Entity Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OtherLicenceEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new OtherLicenceEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetDataForTransportManager()
    {
        $id = 7;

        $this->expectOneRestCall('OtherLicence', 'GET', ['transportManager' => $id])
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getDataForTransportManager($id));
    }

    /**
     * Test get by id
     *
     * @group otherLicence
     */
    public function testGetById()
    {
        $id = 7;

        $this->expectOneRestCall('OtherLicence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getById($id));
    }

    /**
     * Test get by TM application id
     *
     * @group otherLicence
     */
    public function testGetByTmApplicationId()
    {
        $id = 7;

        $this->expectOneRestCall('OtherLicence', 'GET', ['transportManagerApplication' => $id])
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getByTmApplicationId($id));
    }

    /**
     * Test get by TM licence id
     *
     * @group otherLicence
     */
    public function testGetByTmLicenceId()
    {
        $id = 7;

        $this->expectOneRestCall('OtherLicence', 'GET', ['transportManagerLicence' => $id])
            ->will($this->returnValue(['Results' => 'RESPONSE']));

        $this->assertEquals('RESPONSE', $this->sut->getByTmLicenceId($id));
    }

    /**
     * @group entity_services
     */
    public function testGetForApplicationAndType()
    {
        $id = 7;
        $prevLicType = 3;

        $data = array(
            'application' => $id,
            'previousLicenceType' => $prevLicType,
            'limit' => 'all'
        );

        $expected = array('foo');
        $response = array('Results' => $expected);

        $this->expectOneRestCall('OtherLicence', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getForApplicationAndType($id, $prevLicType));
    }
}
