<?php

/**
 * LicenceVehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Mockery as m;
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

    /**
     * @group entity_services
     */
    public function testGetExistingForApplication()
    {
        $applicationId = 69;

        $query = array(
            'removalDate' => 'NULL',
            'application' => $applicationId,
            'limit' => 'all'
        );

        $response = array(
            'Results' => 'RESPONSE'
        );

        $this->expectOneRestCall('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getExistingForApplication($applicationId));
    }

    /**
     * @group entity_services
     */
    public function testRemoveVehicles()
    {
        $ids = [1, 2];
        $date = '2015-03-24';

        $this->mockDate($date);

        $expectedData = [
            0 => [
                'id' => 1,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            1 => [
                'id' => 2,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            '_OPTIONS_' => ['multiple' => true],
        ];

        $this->expectOneRestCall('LicenceVehicle', 'PUT', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->removeVehicles($ids));
    }

    /**
     * @group entity_services
     */
    public function testRemoveForApplication()
    {

        $applicationId = 69;
        $date = '2015-03-24';

        $this->mockDate($date);

        $query = array(
            'removalDate' => 'NULL',
            'application' => $applicationId,
            'limit' => 'all'
        );
        $response = array(
            'Results' => [
                ['id' => 1],
                ['id' => 2],
            ]
        );

        $this->expectedRestCallInOrder('LicenceVehicle', 'GET', $query)
            ->will($this->returnValue($response));

        $expectedData = [
            0 => [
                'id' => 1,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            1 => [
                'id' => 2,
                'removalDate' => $date,
                '_OPTIONS_' => ['force' => true],
            ],
            '_OPTIONS_' => ['multiple' => true],
        ];

        $this->expectedRestCallInOrder('LicenceVehicle', 'PUT', $expectedData)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->removeForApplication($applicationId));
    }
}
