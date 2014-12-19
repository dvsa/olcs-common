<?php

/**
 * VehicleHistoryView Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\VehicleHistoryViewEntityService;

/**
 * VehicleHistoryView Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleHistoryViewEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new VehicleHistoryViewEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetDataForVrm()
    {
        $vrm = 'AB12CDE';

        $data = array(
            'vrm' => $vrm,
            'sort' => 'specifiedDate',
            'order' => 'DESC',
            'limit' => 'all'
        );

        $this->expectOneRestCall('VehicleHistoryView', 'GET', $data)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForVrm($vrm));
    }
}
