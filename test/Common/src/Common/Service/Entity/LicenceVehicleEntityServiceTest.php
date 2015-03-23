<?php

/**
 * LicenceVehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\LicenceVehicleEntityService;

/**
 * LicenceVehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicleEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new LicenceVehicleEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetVehicle()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVehicle($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclePsv()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVehiclePsv($id));
    }

    /**
     * @group entity_services
     */
    public function testDelete()
    {
        $id = 3;

        $date = date('Y-m-d');

        $this->mockDate($date);

        $data = array(
            'id' => $id,
            'removalDate' => $date,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $this->expectOneRestCall('LicenceVehicle', 'PUT', $data);

        $this->sut->delete($id);
    }

    /**
     * @group entity_services
     */
    public function testCeaseActiveDiscWithoutDiscs()
    {
        $id = 3;

        $data = array(
            'goodsDiscs' => array()
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertNull($this->sut->ceaseActiveDisc($id));
    }

    /**
     * @group entity_services
     */
    public function testCeaseActiveDiscWithCeasedDisc()
    {
        $id = 3;

        $data = array(
            'goodsDiscs' => array(
                array(
                    'ceasedDate' => '2010-01-01'
                )
            )
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertNull($this->sut->ceaseActiveDisc($id));
    }

    /**
     * @group entity_services
     */
    public function testCeaseActiveDisc()
    {
        $id = 3;

        $data = array(
            'goodsDiscs' => array(
                array(
                    'id' => 1,
                    'ceasedDate' => null
                )
            )
        );

        $date = date('Y-m-d');

        $this->mockDate($date);

        $mockGoodsDiscService = $this->getMock('\stdClass', array('save'));
        $mockGoodsDiscService->expects($this->once())
            ->method('save')
            ->with(array('id' => 1, 'ceasedDate' => $date));

        $this->sm->setService('Entity\GoodsDisc', $mockGoodsDiscService);

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertNull($this->sut->ceaseActiveDisc($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDiscPendingData()
    {
        $id = 3;

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDiscPendingData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVrm()
    {
        $id = 3;

        $data = array(
            'vehicle' => array(
                'vrm' => 'foo'
            )
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $id)
            ->will($this->returnValue($data));

        $this->assertEquals('foo', $this->sut->getVrm($id));
    }

    /**
     * @group entity_services
     */
    public function testGetCurrentVrmsForLicence()
    {
        $id = 3;

        $data = array(
            'Count' => 3,
            'Results' => array(
                array(
                    'vehicle' => array(
                        'vrm' => 'foo'
                    )
                ),
                array(
                    'vehicle' => array(
                        'vrm' => 'bar'
                    )
                ),
                array(
                    'vehicle' => array(
                        'vrm' => 'cake'
                    )
                ),
            )
        );

        $params = array(
            'licence' => $id,
            'removalDate' => 'NULL',
            'limit' => 'all'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $params)
            ->will($this->returnValue($data));

        $this->assertEquals(array('foo', 'bar', 'cake'), $this->sut->getCurrentVrmsForLicence($id));
    }

    /**
     * @group entity_services
     */
    public function testGetForApplicationValidation()
    {
        $id = 3;
        $applicationId = 8;

        $query = array(
            'licence' => $id,
            'removalDate' => 'NULL',
            'limit' => 'all',
            'application' => $applicationId
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getForApplicationValidation($id, $applicationId));
    }

    /**
     * @group entity_services
     */
    public function testExistingForLicence()
    {
        $id = 3;
        $applicationId = 8;

        $query = array(
            'licence' => $id,
            'specifiedDate' => 'NOT NULL',
            'removalDate' => 'NULL',
            'interimApplication' => 'NULL',
            'application' => '!= ' . $applicationId,
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getExistingForLicence($id, $applicationId));
    }
}
