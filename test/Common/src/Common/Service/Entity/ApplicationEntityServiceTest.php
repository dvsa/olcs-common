<?php

/**
 * Application Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\RefData;
use Common\Service\Entity\ApplicationEntityService;
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
     * @expectedException \Common\Exception\DataServiceException
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

        $this->assertEquals(RefData::APPLICATION_TYPE_VARIATION, $this->sut->getApplicationType($id));
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

        $this->assertEquals(RefData::APPLICATION_TYPE_NEW, $this->sut->getApplicationType($id));
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

        $mockLicenceService = $this->createPartialMock('\stdClass', ['delete']);
        $mockLicenceService->expects($this->once())
            ->method('delete')
            ->with(5);

        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $this->sut->delete($id);
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
            'status' => RefData::APPLICATION_STATUS_NOT_SUBMITTED,
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

        $mockApplicationTrackingService = $this->createPartialMock('\stdClass', array('save'));
        $mockApplicationTrackingService->expects($this->once())
            ->method('save')
            ->with(['application' => 5]);

        $this->sm->setService('Entity\VariationCompletion', $mockVariationCompletion);
        $this->sm->setService('Entity\ApplicationTracking', $mockApplicationTrackingService);

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

    public function testGetApplicationsForLicence()
    {
        $licenceId = 69;

        $this->expectOneRestCall(
            'Application',
            'GET',
            ['licence' => $licenceId],
            [
                'children' => [
                    'status',
                    'interimStatus',
                ]
            ]
        )
        ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getApplicationsForLicence($licenceId));
    }

    public function testGetTmHeaderData()
    {
        $this->expectOneRestCall('Application', 'GET', 111)
            ->will($this->returnValue('RESPONSE'));

        $this->assertEquals('RESPONSE', $this->sut->getTmHeaderData(111));
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
                        'interimCurrentStatus' => RefData::INTERIM_STATUS_REQUESTED,
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
                    'interimStatus' => RefData::INTERIM_STATUS_REQUESTED,
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
                        'version' => 2,
                        'interimCurrentStatus' => RefData::INTERIM_STATUS_REQUESTED
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
            ],
            'set_inforce' => [
                // form data
                [
                    'data' => [
                        'interimReason' => 'reason',
                        'interimStart' => '2014/01/01',
                        'interimEnd' => '2015/01/01',
                        'interimAuthVehicles' => 10,
                        'interimAuthTrailers' => 20,
                        'interimCurrentStatus' => RefData::INTERIM_STATUS_INFORCE,
                        'id' => 1,
                        'version' => 2
                    ],
                    'operatingCentres' => [
                        'id' => [1, 2]
                    ],
                    'vehicles' => [
                        'id' => [1, 2]
                    ],
                    'interimStatus' => [
                        'status' => RefData::INTERIM_STATUS_INFORCE
                    ]
                ],
                // save application data
                [
                    'interimReason' => 'reason',
                    'interimStart' => '2014/01/01',
                    'interimEnd' => '2015/01/01',
                    'interimAuthVehicles' => 10,
                    'interimAuthTrailers' => 20,
                    'interimStatus' => RefData::INTERIM_STATUS_INFORCE,
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
                        'interimApplication' => 1,
                        'specifiedDate' => '2015-01-01'
                    ],
                    [
                        'id' => 2,
                        'version' => 2,
                        'interimApplication' => 1,
                        'specifiedDate' => '2015-01-01'
                    ],
                    [
                        'id' => 3,
                        'version' => 2,
                        'interimApplication' => 'NULL',
                        'specifiedDate' => null
                    ],
                    [
                        'id' => 4,
                        'version' => 2,
                        'interimApplication' => 'NULL',
                        'specifiedDate' => null
                    ]
                ],
                // type
                true
            ]
        ];
    }
}
