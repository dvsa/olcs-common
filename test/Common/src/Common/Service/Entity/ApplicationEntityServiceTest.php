<?php

/**
 * Application Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Entity\LicenceEntityService;
use Mockery as m;

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
            'status' => LicenceEntityService::LICENCE_STATUS_NOT_SUBMITTED,
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

        $trackingData = array(
            'application' => 4
        );
        $trafficAreaId = 'B';

        $mockLicenceService = $this->getMock('\stdClass', array('save', 'setTrafficArea'));
        $mockLicenceService->expects($this->once())
            ->method('save')
            ->with($licenceData)
            ->will($this->returnValue($licenceResponse));

        $mockLicenceService->expects($this->once())
            ->method('setTrafficArea')
            ->with(7, $trafficAreaId);

        $mockApplicationCompletionService = $this->getMock('\stdClass', array('save'));
        $mockApplicationCompletionService->expects($this->once())
            ->method('save')
            ->with($completionData);

        $mockApplicationTrackingService = $this->getMock('\stdClass', array('save'));
        $mockApplicationTrackingService->expects($this->once())
            ->method('save')
            ->with($trackingData);

        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletionService);
        $this->sm->setService('Entity\ApplicationTracking', $mockApplicationTrackingService);

        $this->expectOneRestCall('Application', 'POST', $applicationData)
            ->will($this->returnValue($applicationResponse));

        $this->assertEquals(
            array('application' => 4, 'licence' => 7),
            $this->sut->createNew($orgId, [], $trafficAreaId)
        );
    }

    /**
     * @group entity_services
     */
    public function testCreateNewWithUtility()
    {
        $orgId = 3;

        $licenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_NOT_SUBMITTED,
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

        $trackingData = array(
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

        $mockApplicationTrackingService = $this->getMock('\stdClass', array('save'));
        $mockApplicationTrackingService->expects($this->once())
            ->method('save')
            ->with($trackingData);

        $mockUtility = m::mock();
        $this->sm->setService('ApplicationUtility', $mockUtility);
        $mockUtility->shouldReceive('alterCreateApplicationData')
            ->with($applicationData)
            ->andReturn($applicationData);

        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\ApplicationCompletion', $mockApplicationCompletionService);
        $this->sm->setService('Entity\ApplicationTracking', $mockApplicationTrackingService);

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

    public function testGetVariationInterimData()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVariationInterimData($id));
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
    public function testGetCategory()
    {
        $id = 4;

        $response = array(
            'goodsOrPsv' => array(
                'id' => 'RESPONSE'
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('RESPONSE', $this->sut->getCategory($id));
    }

    /**
     * @group entity_services
     */
    public function testGetApplicationDateWithoutReceivedDate()
    {
        $id = 4;

        $response = array(
            'receivedDate' => null,
            'createdOn' => '2014-01-01'
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('2014-01-01', $this->sut->getApplicationDate($id));
    }

    /**
     * @group entity_services
     */
    public function testGetApplicationDate()
    {
        $id = 4;

        $response = array(
            'receivedDate' => '2012-01-01',
            'createdOn' => '2014-01-01'
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals('2012-01-01', $this->sut->getApplicationDate($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForProcessing()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForProcessing($id));
    }

    /**
     * @group entity_services
     */
    public function testGetDataForTasks()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForTasks($id));
    }

    /**
     * @group entity_services
     */
    public function testGetOrganisation()
    {
        $id = 4;
        $licenceId = 6;

        $mockLicenceEntity = m::mock();
        $mockLicenceEntity->shouldReceive('getOrganisation')
            ->with($licenceId)
            ->andReturn('foo');

        $response = array(
            'licence' => array(
                'id' => 6
            )
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $this->assertEquals('foo', $this->sut->getOrganisation($id));
    }

    /**
     * @group entity_services
     */
    public function testDelete()
    {
        $id = 3;
        $response = array(
            'licence' => array(
                'id' => 5
            )
        );

        $this->expectedRestCallInOrder('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->expectedRestCallInOrder('Application', 'DELETE', ['id' => $id]);

        $mockLicenceService = $this->getMock('\stdClass', ['delete']);
        $mockLicenceService->expects($this->once())
            ->method('delete')
            ->with(5);

        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->sut->delete($id);
    }

    /**
     * @group entity_services
     */
    public function testGetDataForValidating()
    {
        $id = 3;

        $response = array(
            'licenceType' => array(
                'id' => 'xxx'
            ),
            'goodsOrPsv' => array(
                'id' => 'yyy'
            ),
            'totAuthTrailers' => 50,
            'totAuthVehicles' => 50,
            'totAuthSmallVehicles' => 10,
            'totAuthMediumVehicles' => 20,
            'totAuthLargeVehicles' => 20,
            'niFlag' => 'Y',
            'foo' => 'bar',
            'cake' => 'here'
        );

        $expected = array(
            'licenceType' => 'xxx',
            'goodsOrPsv' => 'yyy',
            'totAuthTrailers' => 50,
            'totAuthVehicles' => 50,
            'totAuthSmallVehicles' => 10,
            'totAuthMediumVehicles' => 20,
            'totAuthLargeVehicles' => 20,
            'niFlag' => 'Y'
        );

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($response));

        $this->assertEquals($expected, $this->sut->getDataForValidating($id));
    }

    /**
     * @group entity_services
     */
    public function testCreateVariation()
    {
        $this->mockDate('2014-01-01');

        $licenceId = 3;
        $stubbedLicenceData = [
            'bar' => 'foo'
        ];
        $applicationData = [
            'foo' => 'bar'
        ];
        $expectedData = [
            'licence' => 3,
            'status' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED,
            'isVariation' => true,
            'foo' => 'bar',
            'bar' => 'foo'
        ];

        $mockLicenceEntity = m::mock();
        $mockLicenceEntity->shouldReceive('getVariationData')
            ->with($licenceId)
            ->andReturn($stubbedLicenceData);

        $this->expectOneRestCall('Application', 'POST', $expectedData)
            ->will($this->returnValue(['id' => 5]));

        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockVariationCompletion = m::mock();
        $mockVariationCompletion->shouldReceive('save')
            ->with(['application' => 5]);

        $mockApplicationTrackingService = $this->getMock('\stdClass', array('save'));
        $mockApplicationTrackingService->expects($this->once())
            ->method('save')
            ->with(['application' => 5]);

        $this->sm->setService('Entity\VariationCompletion', $mockVariationCompletion);
        $this->sm->setService('Entity\ApplicationTracking', $mockApplicationTrackingService);

        $this->assertEquals(5, $this->sut->createVariation($licenceId, $applicationData));
    }

    /**
     * @group entity_services
     */
    public function testCreateVariationWithVariationUtility()
    {
        $this->mockDate('2014-01-01');

        $licenceId = 3;
        $stubbedLicenceData = [
            'bar' => 'foo'
        ];
        $applicationData = [
            'foo' => 'bar'
        ];
        $expectedData = [
            'licence' => 3,
            'status' => ApplicationEntityService::APPLICATION_STATUS_NOT_SUBMITTED,
            'isVariation' => true,
            'foo' => 'bar',
            'bar' => 'foo'
        ];

        $mockLicenceEntity = m::mock();
        $mockLicenceEntity->shouldReceive('getVariationData')
            ->with($licenceId)
            ->andReturn($stubbedLicenceData);

        $this->expectOneRestCall('Application', 'POST', $expectedData)
            ->will($this->returnValue(['id' => 5]));

        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockVariationCompletion = m::mock();
        $mockVariationCompletion->shouldReceive('save')
            ->with(['application' => 5]);

        $mockApplicationTrackingService = $this->getMock('\stdClass', array('save'));
        $mockApplicationTrackingService->expects($this->once())
            ->method('save')
            ->with(['application' => 5]);

        $this->sm->setService('Entity\VariationCompletion', $mockVariationCompletion);
        $this->sm->setService('Entity\ApplicationTracking', $mockApplicationTrackingService);

        $mockVariation = m::mock();
        $this->sm->setService('VariationUtility', $mockVariation);
        $mockVariation->shouldReceive('alterCreateVariationData')
            ->with($expectedData)
            ->andReturn($expectedData);

        $this->assertEquals(5, $this->sut->createVariation($licenceId, $applicationData));
    }

    public function testGetLicenceTotCommunityLicences()
    {
        $id = 3;
        $stubbedData = [
            'licence' => [
                'totCommunityLicences' => 20
            ]
        ];

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue($stubbedData));

        $this->assertEquals(20, $this->sut->getLicenceTotCommunityLicences($id));
    }

    public function testGetDataForUndertakings()
    {
        $id = 123;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForUndertakings($id));
    }

    /**
     * @group entity_services
     */
    public function testGetLicenceType()
    {
        $id = 4;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getLicenceType($id));
    }

    public function testGetVariationCompletionStatusData()
    {
        $id = 3;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getVariationCompletionStatusData($id));
    }

    public function testGetSubmitSummaryData()
    {
        $id = 3;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getSubmitSummaryData($id));
    }

    public function testGetDataForPaymentSubmission()
    {
        $id = 3;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForPaymentSubmission($id));
    }

    public function testGetDataForFinancialEvidence()
    {
        $id = 3;

        $this->expectOneRestCall('Application', 'GET', $id)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getDataForFinancialEvidence($id));
    }

    /**
     * @dataProvider providerGetReviewDataForApplication
     */
    public function testGetReviewDataForApplication($sections, $expectedBundle)
    {
        $id = 123;

        $this->expectOneRestCall('Application', 'GET', $id, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getReviewDataForApplication($id, $sections));
    }

    /**
     * @dataProvider providerGetReviewDataForVariation
     */
    public function testGetReviewDataForVariation($sections, $expectedBundle)
    {
        $id = 123;

        $this->expectOneRestCall('Application', 'GET', $id, $expectedBundle)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getReviewDataForVariation($id, $sections));
    }

    /**
     * Test get data for interim
     * 
     * @group applicationEntity
     */
    public function testGetDataForInterim()
    {
        $id = 123;
        $bundle = array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre' => array(
                            'children' => array(
                                'address'
                            )
                        )
                    )
                ),
                'licenceVehicles' => array(
                    'children' => array(
                        'vehicle',
                        'interimApplication',
                        'goodsDiscs'
                    )
                ),
                'interimStatus',
                'licence' => array(
                    'children' => array(
                        'communityLics' => array(
                            'children' => array(
                                'status'
                            )
                        )
                    )
                )
            )
        );
        $response = [
            'id' => $id,
            'operatingCentres' => [
                ['action' => 'A', 'operatingCentre' => ['address' => 'address1']],
                ['action' => 'U', 'operatingCentre' => ['address' => 'address2']],
                ['action' => '', 'operatingCentre' => ['address' => 'address3']],
            ]
        ];
        $processed = [
            'id' => $id,
            'operatingCentres' => [
                ['action' => 'A', 'address' => 'address1', 'operatingCentre' => ['address' => 'address1']],
                ['action' => 'U', 'address' => 'address2', 'operatingCentre' => ['address' => 'address2']],
            ]
        ];

        $this->expectOneRestCall('Application', 'GET', $id, $bundle)
            ->will($this->returnValue($response));

        $this->assertEquals($processed, $this->sut->getDataForInterim($id));
    }

    /**
     * Test save interim data
     * 
     * @group applicationEntity
     * @dataProvider providerSaveInterimData
     */
    public function testSaveInterimData($formData, $saveData, $saveOcData, $saveLicenceVehicleData, $type)
    {
        $bundle = array(
            'children' => array(
                'operatingCentres' => array(
                    'children' => array(
                        'operatingCentre' => array(
                            'children' => array(
                                'address'
                            )
                        )
                    )
                ),
                'licenceVehicles' => array(
                    'children' => array(
                        'vehicle',
                        'interimApplication',
                        'goodsDiscs'
                    )
                ),
                'interimStatus',
                'licence' => array(
                    'children' => array(
                        'communityLics' => array(
                            'children' => array(
                                'status'
                            )
                        )
                    )
                )
            )
        );

        $expectedResults = array(
            'id' => 1,
            'operatingCentres' => array(
                array(
                    'isInterim' => 'Y',
                    'id' => 3,
                    'version' => 1,
                    'action' => 'A',
                    'operatingCentre' => ['address' => 'address']
                ),
                array(
                    'isInterim' => 'Y',
                    'id' => 4,
                    'version' => 1,
                    'action' => 'U',
                    'operatingCentre' => ['address' => 'address']
                ),
                array(
                    'isInterim' => 'N',
                    'id' => 1,
                    'version' => 1,
                    'action' => 'A',
                    'operatingCentre' => ['address' => 'address']
                ),
                array(
                    'isInterim' => 'N',
                    'id' => 2,
                    'version' => 1,
                    'action' => 'U',
                    'operatingCentre' => ['address' => 'address']
                )
            ),
            'licenceVehicles' => array(
                array(
                    'interimApplication' => null,
                    'id' => 1,
                    'version' => 2
                ),
                array(
                    'interimApplication' => null,
                    'id' => 2,
                    'version' => 2
                ),
                array(
                    'interimApplication' => 1,
                    'id' => 3,
                    'version' => 2
                ),
                array(
                    'interimApplication' => 1,
                    'id' => 4,
                    'version' => 2
                )
            )
        );

        $this->expectedRestCallInOrder('Application', 'PUT', $saveData);

        $this->expectedRestCallInOrder('Application', 'GET', 1, $bundle)
            ->will($this->returnValue($expectedResults));

        $mockApplicationOcService = m::mock()
            ->shouldReceive('multiUpdate')
            ->with($saveOcData)
            ->getMock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockApplicationOcService);

        $mocklicenceVehicleService = m::mock()
            ->shouldReceive('multiUpdate')
            ->with($saveLicenceVehicleData)
            ->getMock();
        $this->sm->setService('Entity\LicenceVehicle', $mocklicenceVehicleService);

        $this->sut->saveInterimData($formData, $type);
    }

    /**
     * Provider set interim data
     */
    public function providerSaveInterimData()
    {
        return [
            'set' => [
                // form data
                [
                    'data' => [
                        'interimReason' => 'reason',
                        'interimStart' => '2014/01/01',
                        'interimEnd' => '2015/01/01',
                        'interimAuthVehicles' => 10,
                        'interimAuthTrailers' => 20,
                        'interimStatus' => ApplicationEntityService::INTERIM_STATUS_REQUESTED,
                        'id' => 1,
                        'version' => 2
                    ],
                    'operatingCentres' => [
                        'id' => [1, 2]
                    ],
                    'vehicles' => [
                        'id' => [1, 2]
                    ]
                ],
                // save application data
                [
                    'interimReason' => 'reason',
                    'interimStart' => '2014/01/01',
                    'interimEnd' => '2015/01/01',
                    'interimAuthVehicles' => 10,
                    'interimAuthTrailers' => 20,
                    'interimStatus' => ApplicationEntityService::INTERIM_STATUS_REQUESTED,
                    'id' => 1,
                    'version' => 2
                ],
                // save application operating centre data
                [
                    [
                        'id' => 1,
                        'version' => 1,
                        'isInterim' => 'Y'
                    ],
                    [
                        'id' => 2,
                        'version' => 1,
                        'isInterim' => 'Y'
                    ],
                    [
                        'id' => 3,
                        'version' => 1,
                        'isInterim' => 'N'
                    ],
                    [
                        'id' => 4,
                        'version' => 1,
                        'isInterim' => 'N'
                    ],
                ],
                // save licence vehicle data
                [
                    [
                        'id' => 1,
                        'version' => 2,
                        'interimApplication' => 1
                    ],
                    [
                        'id' => 2,
                        'version' => 2,
                        'interimApplication' => 1
                    ],
                    [
                        'id' => 3,
                        'version' => 2,
                        'interimApplication' => 'NULL'
                    ],
                    [
                        'id' => 4,
                        'version' => 2,
                        'interimApplication' => 'NULL'
                    ]
                ],
                // type
                true
            ],
            'unset' => [
                // form data
                [
                    'data' => [
                        'id' => 1,
                        'version' => 2
                     ]
                ],
                // save application data
                [
                    'interimReason' => '',
                    'interimStart' => '',
                    'interimEnd' => '',
                    'interimAuthVehicles' => 0,
                    'interimAuthTrailers' => 0,
                    'interimStatus' => '',
                    'id' => 1,
                    'version' => 2
                ],
                // save applicaiton operating centres data
                [
                    [
                        'id' => 3,
                        'version' => 1,
                        'isInterim' => 'N'
                    ],
                    [
                        'id' => 4,
                        'version' => 1,
                        'isInterim' => 'N'
                    ]
                ],
                // save licence data
                [
                    [
                        'id' => 3,
                        'version' => 2,
                        'interimApplication' => 'NULL'
                    ],
                    [
                        'id' => 4,
                        'version' => 2,
                        'interimApplication' => 'NULL'
                    ]
                ],
                // type
                false
            ]
        ];
    }

    public function providerGetReviewDataForApplication()
    {
        return [
            'No sections' => [
                [],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => ['children' => ['organisation' => []]]
                    ]
                ]
            ],
            'Type of licence' => [
                ['type_of_licence'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => ['children' => ['organisation' => []]]
                    ]
                ]
            ],
            'Operating centre' => [
                ['operating_centres'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => [],
                                'trafficArea'
                            ]
                        ],
                        'operatingCentres' => [
                            'children' => [
                                'application',
                                'operatingCentre' => [
                                    'children' => [
                                        'address',
                                        'adDocuments' => [
                                            'children' => [
                                                'application'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Business type' => [
                ['business_type'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => [
                                    'children' => [
                                        'type'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'Business details' => [
                ['business_details'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => [
                                    'children' => [
                                        'type',
                                        'tradingNames',
                                        // @NOTE I think the organisationNatureOfBusiness table should be a straight
                                        // many-to-many so this could change
                                        'natureOfBusinesss' => [
                                            'children' => [
                                                'refData'
                                            ]
                                        ],
                                        'contactDetails' => [
                                            'children' => [
                                                'address'
                                            ]
                                        ]
                                    ]
                                ],
                                'companySubsidiaries' => [
                                    'children' => [
                                        'companySubsidiary'
                                    ]
                                ],
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function providerGetReviewDataForVariation()
    {
        return [
            'No sections' => [
                [],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => []
                            ]
                        ]
                    ]
                ]
            ],
            // Same as base
            'Business type' => [
                ['business_type'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => []
                            ]
                        ]
                    ]
                ]
            ],
            // Same as base
            'Business details' => [
                ['business_details'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => []
                            ]
                        ]
                    ]
                ]
            ],
            'Type of licence' => [
                ['type_of_licence'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => [],
                                'licenceType'
                            ]
                        ]
                    ]
                ]
            ],
            'Operating centre' => [
                ['operating_centres'],
                [
                    'children' => [
                        'licenceType',
                        'goodsOrPsv',
                        'licence' => [
                            'children' => [
                                'organisation' => [],
                                'trafficArea'
                            ]
                        ],
                        'operatingCentres' => [
                            'children' => [
                                'application',
                                'operatingCentre' => [
                                    'children' => [
                                        'address',
                                        'adDocuments' => [
                                            'children' => [
                                                'application'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
