<?php

/**
 * Licence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\LicenceEntityService;

/**
 * Licence Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new LicenceEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetOverview()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getOverview($id));
    }

    /**
     * @group entity_services
     */
    public function testGetTypeOfLicenceData()
    {
        $id = 7;

        $response = array(
            'version' => 3,
            'niFlag' => 'Y',
            'licenceType' => array(
                'id' => 1
            ),
            'goodsOrPsv' => array(
                'id' => 2
            )
        );

        $expected = array(
            'version' => 3,
            'niFlag' => 'Y',
            'licenceType' => 1,
            'goodsOrPsv' => 2
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getTypeOfLicenceData($id));
        // Test the cache
        $this->assertEquals($expected, $this->sut->getTypeOfLicenceData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetTypeOfLicenceDataWithNulls()
    {
        $id = 7;

        $response = array(
            'version' => 3,
            'niFlag' => 'Y',
            'licenceType' => array(),
            'goodsOrPsv' => array()
        );

        $expected = array(
            'version' => 3,
            'niFlag' => 'Y',
            'licenceType' => null,
            'goodsOrPsv' => null
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getTypeOfLicenceData($id));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisationWithoutOrgId()
    {
        $id = 7;
        $orgId = 3;

        $response = array(

        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertFalse($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisationWithMisMatchedOrgId()
    {
        $id = 7;
        $orgId = 3;

        $response = array(
            'organisation' => array(
                'id' => 5
            )
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertFalse($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisation()
    {
        $id = 7;
        $orgId = 3;

        $response = array(
            'organisation' => array(
                'id' => 3
            )
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertTrue($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testGetHeaderParams()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getHeaderParams($id));
    }

    /**
     * @group entity_services
     */
    public function testGetAddressesData()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getAddressesData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetSafetyData()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getSafetyData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclesData()
    {
        $id = 7;

        $response = array(
            'licenceVehicles' => 'foo'
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('foo', $this->sut->getVehiclesData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclesPsvData()
    {
        $id = 7;

        $response = array(
            'licenceVehicles' => 'foo'
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('foo', $this->sut->getVehiclesPsvData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetCurrentVrms()
    {
        $id = 7;

        $mockLicenceVehicleService = $this->getMock('\stdClass', array('getCurrentVrmsForLicence'));
        $mockLicenceVehicleService->expects($this->once())
            ->method('getCurrentVrmsForLicence')
            ->will($this->returnValue('RESPONSE'));

        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicleService);

        $this->assertEquals('RESPONSE', $this->sut->getCurrentVrms($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclesTotal()
    {
        $id = 7;

        $response = array(
            'licenceVehicles' => array(
                'foo',
                'bar'
            )
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(2, $this->sut->getVehiclesTotal($id));
    }

    /**
     * @group entity_services
     */
    public function testGetVehiclesPsvTotal()
    {
        $id = 7;
        $type = 1;

        $response = array(
            'licenceVehicles' => array(
                array(
                    'vehicle' => array(
                        'psvType' => array(
                            'id' => 1
                        )
                    )
                ),
                array(
                    'vehicle' => array(
                        'psvType' => array(
                            'id' => 1
                        )
                    )
                ),
                array(
                    'vehicle' => array(
                        'psvType' => array(
                            'id' => 3
                        )
                    )
                ),
                array(
                    'vehicle' => array(
                        'psvType' => array(
                            'id' => 4
                        )
                    )
                ),
                array(
                    'vehicle' => array(
                        'psvType' => array()
                    )
                )
            )
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(2, $this->sut->getVehiclesPsvTotal($id, $type));
    }

    /**
     * @group entity_services
     */
    public function testGetTrafficAreaWithoutTrafficArea()
    {
        $id = 7;

        $response = array('foo' => 'bar');

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertNull($this->sut->getTrafficArea($id));
    }

    /**
     * @group entity_services
     */
    public function testGetTrafficArea()
    {
        $id = 7;

        $response = array(
            'trafficArea' => 'foo'
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('foo', $this->sut->getTrafficArea($id));
    }

    /**
     * @group entity_services
     */
    public function testGetTotalAuths()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTotalAuths($id));
    }

    /**
     * @group entity_services
     */
    public function testGetPsvDiscsRequestData()
    {
        $id = 7;

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getPsvDiscsRequestData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetPsvDiscs()
    {
        $id = 7;

        $response = array(
            'psvDiscs' => 'RESPONSE'
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getPsvDiscs($id));
    }

    /**
     * @group entity_services
     */
    public function testSetTrafficAreaWithNullTrafficArea()
    {
        $licenceId = 4;
        $trafficAreaId = null;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => null,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $this->expectOneRestCall('Licence', 'PUT', $data);

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     */
    public function testSetTrafficAreaWithoutLicenceData()
    {
        $licenceId = 4;
        $trafficAreaId = 5;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => 5,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $licenceData = array(

        );

        $this->expectedRestCallInOrder('Licence', 'PUT', $data);
        $this->expectedRestCallInOrder('Licence', 'GET', $licenceId)
            ->will($this->returnValue($licenceData));

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Error generating licence
     */
    public function testSetTrafficAreaEmptyLicenceNoWithFailedGeneration()
    {
        $licenceId = 4;
        $trafficAreaId = 5;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => 5,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $licenceData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => null,
            'applications' => array(
                array(
                    'goodsOrPsv' => array(
                        'id' => 1
                    )
                )
            ),
            'trafficArea' => array(
                'id' => 2
            )
        );

        $this->expectedRestCallInOrder('Licence', 'PUT', $data);
        $this->expectedRestCallInOrder('Licence', 'GET', $licenceId)
            ->will($this->returnValue($licenceData));

        $mockLicenceNoGenService = $this->getMock('\stdClass', array('save'));
        $mockLicenceNoGenService->expects($this->once())
            ->method('save')
            ->with(array('licence' => $licenceId))
            ->will($this->returnValue(array()));

        $this->sm->setService('Entity\LicenceNoGen', $mockLicenceNoGenService);

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     */
    public function testSetTrafficAreaEmptyLicenceNo()
    {
        $licenceId = 4;
        $trafficAreaId = 5;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => 5,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $licenceData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => null,
            'applications' => array(
                array(
                    'goodsOrPsv' => array(
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    )
                )
            ),
            'trafficArea' => array(
                'id' => 'A'
            )
        );

        $saveData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => 'OA13'
        );

        $this->expectedRestCallInOrder('Licence', 'PUT', $data);
        $this->expectedRestCallInOrder('Licence', 'GET', $licenceId)
            ->will($this->returnValue($licenceData));
        $this->expectedRestCallInOrder('Licence', 'PUT', $saveData);

        $mockLicenceNoGenService = $this->getMock('\stdClass', array('save'));
        $mockLicenceNoGenService->expects($this->once())
            ->method('save')
            ->with(array('licence' => $licenceId))
            ->will($this->returnValue(array('id' => 13)));

        $this->sm->setService('Entity\LicenceNoGen', $mockLicenceNoGenService);

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     */
    public function testSetTrafficAreaWithLicenceNoWithoutTrafficAreaChange()
    {
        $licenceId = 4;
        $trafficAreaId = 5;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => 5,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $licenceData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => 'OA13',
            'goodsOrPsv' => array(
                'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
            ),
            'trafficArea' => array(
                'id' => 'A'
            )
        );

        $this->expectedRestCallInOrder('Licence', 'PUT', $data);
        $this->expectedRestCallInOrder('Licence', 'GET', $licenceId)
            ->will($this->returnValue($licenceData));

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     */
    public function testSetTrafficAreaWithLicenceNo()
    {
        $licenceId = 4;
        $trafficAreaId = 5;

        $data = array(
            'id' => $licenceId,
            'trafficArea' => 5,
            '_OPTIONS_' => array(
                'force' => true
            )
        );

        $licenceData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => 'OA13',
            'applications' => array(
                array(
                    'goodsOrPsv' => array(
                        'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                    ),
                )
            ),
            'trafficArea' => array(
                'id' => 'B'
            )
        );

        $saveData = array(
            'id' => $licenceId,
            'version' => 3,
            'licNo' => 'OB13'
        );

        $this->expectedRestCallInOrder('Licence', 'PUT', $data);
        $this->expectedRestCallInOrder('Licence', 'GET', $licenceId)
            ->will($this->returnValue($licenceData));
        $this->expectedRestCallInOrder('Licence', 'PUT', $saveData);

        $this->sut->setTrafficArea($licenceId, $trafficAreaId);
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithResult()
    {
        $response = [
            'Count' => 1,
            'Results' => [
                'RESPONSE'
            ]
        ];
        $this->expectOneRestCall('Licence', 'GET', ['licNo' => 123])
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testFindByIdentifierWithNoResult()
    {
        $response = [
            'Count' => 0,
            'Results' => []
        ];
        $this->expectOneRestCall('Licence', 'GET', ['licNo' => 123])
            ->will($this->returnValue($response));

        $this->assertEquals(false, $this->sut->findByIdentifier(123));
    }

    /**
     * @group entity_services
     */
    public function testGetVariationData()
    {
        $id = 4;
        $stubbedData = [
            'foo' => 'bar',
            'ignore' => 'this',
            'totAuthTrailers' => 1,
            'totAuthVehicles' => 2,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5,
            'licenceType' => ['id' => 6],
            'niFlag' => 'N',
            'goodsOrPsv' => ['id' => 'xyz']
        ];
        $expected = [
            'totAuthTrailers' => 1,
            'totAuthVehicles' => 2,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 4,
            'totAuthLargeVehicles' => 5,
            'licenceType' => 6,
            'niFlag' => 'N',
            'goodsOrPsv' => 'xyz'
        ];

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($stubbedData));

        $this->assertEquals($expected, $this->sut->getVariationData($id));
    }

    public function testGetOrganisation()
    {
        $id = 4;

        $response = array(
            'organisation' => 'foo'
        );

        $this->expectOneRestCall('Licence', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('foo', $this->sut->getOrganisation($id));
    }
}
