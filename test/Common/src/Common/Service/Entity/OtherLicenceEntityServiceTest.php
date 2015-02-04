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
    public function testGetData()
    {
        $id = 7;

        $this->expectOneRestCall('OtherLicence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getData($id));
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
}
