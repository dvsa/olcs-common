<?php

/**
 * Vehicle Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\RefData;
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

    /**
     * @dataProvider psvTypeFromTypeProvider
     * @param string $type
     * @param string $expectedPsvType
     */
    public function testGetPsvTypeFromType($type, $expectedPsvType)
    {
        $this->assertEquals($expectedPsvType, $this->sut->getPsvTypeFromType($type));
    }

    public function psvTypeFromTypeProvider()
    {
        return [
            ['small', RefData::PSV_TYPE_SMALL],
            ['medium', RefData::PSV_TYPE_MEDIUM],
            ['large', RefData::PSV_TYPE_LARGE],
        ];
    }

    /**
     * @dataProvider typeFromPsvTypeProvider
     * @param string $psvType
     * @param string $expectedType
     */
    public function testGetTypeFromPsvType($psvType, $expectedType)
    {
        $this->assertEquals($expectedType, $this->sut->getTypeFromPsvType($psvType));
    }

    public function typeFromPsvTypeProvider()
    {
        return [
            [RefData::PSV_TYPE_SMALL, 'small'],
            [RefData::PSV_TYPE_MEDIUM, 'medium'],
            [RefData::PSV_TYPE_LARGE, 'large'],
        ];
    }
}
