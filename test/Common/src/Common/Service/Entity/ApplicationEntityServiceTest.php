<?php

/**
 * Application Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ApplicationEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     */
    public function testGetForOrganisation()
    {
        $orgId = 3;

        $mockOrganisationEntityService = $this->getMock('\stdClass', array('getApplications'));

        $mockOrganisationEntityService->expects($this->once())
            ->method('getApplications')
            ->with($orgId)
            ->will($this->returnValue('RESPONSE'));

        $this->sm->setService('Entity\Organisation', $mockOrganisationEntityService);

        $this->assertEquals('RESPONSE', $this->sut->getForOrganisation($orgId));
    }

    /**
     * @group entity_services
     */
    public function testCreateNew()
    {
        $orgId = 3;

        $licenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_NEW,
            'organisation' => $orgId
        );

        $licenceResponse = array(
            'id' => 7
        );

        $applicationData = array(
            'licence' => 7,
            'status' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED,
            'isVariation' => false
        );

        $applicationResponse = array(
            'id' => 4
        );

        $completionData = array(
            'application' => 4
        );

        $mockLicenceService = $this->getMock('\stdClass', array('save'));
        $mockLicenceService->expects($this->once())
            ->method('save')
            ->with($licenceData)
            ->will($this->returnValue($licenceResponse));

        $mockApplicationCompletionService = $this->getMock('\stdClass', array('save'));
        $mockApplicationCompletionService->expects($this->once())
            ->method('save')
            ->with($completionData);

        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletionService);

        $this->expectOneRestCall('Application', 'POST', $applicationData)
            ->will($this->returnValue($applicationResponse));

        $this->assertEquals(array('application' => 4, 'licence' => 7), $this->sut->createNew($orgId));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisationNoResponse()
    {
        $id = 4;
        $orgId = 3;

        $response = array(
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertFalse($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisationMisMatch()
    {
        $id = 4;
        $orgId = 3;

        $response = array(
            'licence' => array(
                'organisation' => array(
                    'id' => 7
                )
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertFalse($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testDoesBelongToOrganisation()
    {
        $id = 4;
        $orgId = 3;

        $response = array(
            'licence' => array(
                'organisation' => array(
                    'id' => 3
                )
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertTrue($this->sut->doesBelongToOrganisation($id, $orgId));
    }

    /**
     * @group entity_services
     */
    public function testGetOverview()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getOverview($id));
    }

    /**
     * @group entity_services
     */
    public function testGetLicenceIdForApplication()
    {
        $id = 4;

        $response = array(
            'licence' => array(
                'id' => 3
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(3, $this->sut->getLicenceIdForApplication($id));

        // Make the same assertion again to test the caching
        $this->assertEquals(3, $this->sut->getLicenceIdForApplication($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForCompletionStatus()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForCompletionStatus($id));
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Is variation flag not found
     */
    public function testGetApplicationTypeWithoutIsVariationFlag()
    {
        $id = 4;

        $response = array();

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->sut->getApplicationType($id);
    }

    /**
     * @group entity_services
     */
    public function testGetApplicationTypeVariation()
    {
        $id = 4;

        $response = array(
            'isVariation' => true
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(ApplicationEntityService::APPLICATION_TYPE_VARIATION, $this->sut->getApplicationType($id));
    }

    /**
     * @group entity_services
     */
    public function testGetApplicationTypeNew()
    {
        $id = 4;

        $response = array(
            'isVariation' => false
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals(ApplicationEntityService::APPLICATION_TYPE_NEW, $this->sut->getApplicationType($id));
    }

    /**
     * @group entity_services
     */
    public function testGetHeaderData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getHeaderData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetSafetyData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getSafetyData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetFinancialHistoryData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getFinancialHistoryData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetLicenceHistoryData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getLicenceHistoryData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetConvictionsPenaltiesData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getConvictionsPenaltiesData($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForVehiclesDeclarations()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForVehiclesDeclarations($id));
    }

    /**
     * @group entity_services
     */
    public function testGetStatus()
    {
        $id = 4;

        $response = array(
            'status' => array(
                'id' => 'RESPONSE'
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getStatus($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForVehiclesPsv()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForVehiclesPsv($id));
    }
}
