<?php

/**
 * Vehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\VehicleEntityService;

/**
 * Vehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VehicleEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new VehicleEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     * @group vehicle_entity_service
     */
    public function testGetLicencesForVrm()
    {
        $vrm = 'AB12CDE';

        $data = array('vrm' => $vrm);
        $response = array(
            'Results' => array(
                array(
                    'licenceVehicles' => array(
                        array(
                            'foo',
                            'removalDate' => null,
                        ),
                        array('bar')
                    )
                ),
                array(
                    'licenceVehicles' => array(
                        array(
                            'cake',
                            'removalDate' => 'xxx',
                        ),
                        array(
                            'jazz',
                            'removalDate' => null,
                        )
                    )
                )
            )
        );

        // we should get back everything that *doesn't* have a removed date
        $expected = array(
            array(
                'foo',
                'removalDate' => null
            ),
            array('bar'),
            array(
                'jazz',
                'removalDate' => null
            )
        );

        $this->expectOneRestCall('Vehicle', 'GET', $data)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getLicencesForVrm($vrm));
    }
}
