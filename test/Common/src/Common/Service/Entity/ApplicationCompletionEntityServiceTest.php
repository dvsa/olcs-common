<?php

/**
 * Application Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use Common\Service\Entity\ApplicationCompletionEntityService;
use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Entity\ContactDetailsEntityService;
use Common\Service\Entity\LicenceEntityService;
use Common\Service\Entity\VehicleEntityService;

/**
 * Application Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCompletionEntityServiceTest extends AbstractEntityServiceTestCase
{
    protected function setUp()
    {
        $this->sut = new ApplicationCompletionEntityService();

        parent::setUp();
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Completions status not found
     */
    public function testGetCompletionStatusesWithNoRecord()
    {
        $applicationId = 3;

        $data = array(
            'Count' => 0
        );

        $this->expectOneRestCall('ApplicationCompletion', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->sut->getCompletionStatuses($applicationId);
    }

    /**
     * @group entity_services
     *
     * @expectedException \Common\Service\Entity\Exceptions\UnexpectedResponseException
     * @expectedExceptionMessage Too many completion statuses found
     */
    public function testGetCompletionStatusesWithTooManyRecords()
    {
        $applicationId = 3;

        $data = array(
            'Count' => 2
        );

        $this->expectOneRestCall('ApplicationCompletion', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->sut->getCompletionStatuses($applicationId);
    }

    /**
     * @group entity_services
     */
    public function testGetCompletionStatuses()
    {
        $applicationId = 3;

        $expected = array(
            'sample' => 'result'
        );

        $data = array(
            'Count' => 1,
            'Results' => array(
                $expected
            )
        );

        $this->expectOneRestCall('ApplicationCompletion', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($data));

        $this->assertEquals($expected, $this->sut->getCompletionStatuses($applicationId));
    }

    /**
     * @group entity_services
     *
     * @dataProvider providerForTestUpdateCompletionStatuses
     */
    public function testUpdateCompletionStatuses(
        $currentSection,
        $completionData,
        $applicationData,
        $expectedCompletionStatusData
    ) {
        $applicationId = 3;

        $mockApplicationEntityService = $this->getMock('\stdClass', array('getDataForCompletionStatus'));
        $mockApplicationEntityService->expects($this->once())
            ->method('getDataForCompletionStatus')
            ->with($applicationId)
            ->will($this->returnValue($applicationData));

        $mockedCompletionData = array(
            'Count' => 1,
            'Results' => array(
                $completionData
            )
        );

        $this->restHelper->expects($this->at(0))
            ->method('makeRestCall')
            ->with('ApplicationCompletion', 'GET', ['application' => $applicationId])
            ->will($this->returnValue($mockedCompletionData));

        $this->restHelper->expects($this->at(1))
            ->method('makeRestCall')
            ->with('ApplicationCompletion', 'PUT', $expectedCompletionStatusData);

        $this->sm->setService('Entity\Application', $mockApplicationEntityService);

        $this->sut->updateCompletionStatuses($applicationId, $currentSection);
    }

    public function providerForTestUpdateCompletionStatuses()
    {
        $initialStatus = array(
            'id' => 3,
            'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
            'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
        );

        $dataSets = array(
            // Incomplete type of licence data
            'type_of_licence_1' => array(
                // Current section
                'type_of_licence',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'niFlag' => null,
                    'goodsOrPsv' => array(
                        'id' => 1
                    ),
                    'licenceType' => array(
                        'id' => 1
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Complete type of licence data
            'type_of_licence_2' => array(
                // Current section
                'type_of_licence',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'niFlag' => 'Y',
                    'goodsOrPsv' => array(
                        'id' => 1
                    ),
                    'licenceType' => array(
                        'id' => 1
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Missing business type data
            'business_type_1' => array(
                // Current section
                'business_type',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'type' => array(
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Complete business type data
            'business_type_2' => array(
                // Current section
                'business_type',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'type' => array(
                                'id' => 1
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details not started
            'business_details_1' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'type' => array(
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details incomplete llp
            'business_details_2' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'name' => 'Foo',
                            'companyOrLlpNo' => 12345678,
                            'type' => array(
                                'id' => OrganisationEntityService::ORG_TYPE_LLP
                            ),
                            'contactDetails' => array(),
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete llp
            'business_details_3' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'name' => 'Foo',
                            'companyOrLlpNo' => 12345678,
                            'type' => array(
                                'id' => OrganisationEntityService::ORG_TYPE_LLP
                            ),
                            'contactDetails' => array(
                                array(
                                    'contactType' => array(
                                        'id' => ContactDetailsEntityService::CONTACT_TYPE_REGISTERED
                                    )
                                )
                            ),
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details incomplete partnership
            'business_details_4' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'type' => array(
                                'id' => OrganisationEntityService::ORG_TYPE_PARTNERSHIP
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete partnership
            'business_details_5' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'name' => 'Bob',
                            'type' => array(
                                'id' => OrganisationEntityService::ORG_TYPE_PARTNERSHIP
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete sole trader
            'business_details_6' => array(
                // Current section
                'business_details',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'type' => array(
                                'id' => OrganisationEntityService::ORG_TYPE_SOLE_TRADER
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses incomplete 1
            'addresses_1' => array(
                // Current section
                'addresses',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceType' => array(
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                        ),
                        'organisation'=> array(
                            'contactDetails' => array(

                            )
                        ),
                        'contactDetails' => array(
                            array(
                                'contactType' => array(
                                    'id' => ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE
                                ),
                                'phoneContacts' => array(
                                    array(
                                        'phoneNumber' => '00000111222'
                                    )
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses complete 1
            'addresses_2' => array(
                // Current section
                'addresses',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceType' => array(
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                        ),
                        'organisation'=> array(
                            'contactDetails' => array(
                                array(
                                    'contactType' => array(
                                        'id' => ContactDetailsEntityService::CONTACT_TYPE_ESTABLISHMENT
                                    )
                                )

                            )
                        ),
                        'contactDetails' => array(
                            array(
                                'contactType' => array(
                                    'id' => ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE
                                ),
                                'phoneContacts' => array(
                                    array(
                                        'phoneNumber' => '00000111222'
                                    )
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses complete 2
            'addresses_3' => array(
                // Current section
                'addresses',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceType' => array(
                            'id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                        ),
                        'organisation'=> array(
                            'contactDetails' => array(

                            )
                        ),
                        'contactDetails' => array(
                            array(
                                'contactType' => array(
                                    'id' => ContactDetailsEntityService::CONTACT_TYPE_CORRESPONDENCE
                                ),
                                'phoneContacts' => array(
                                    array(
                                        'phoneNumber' => '00000111222'
                                    )
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // People incomplete
            'people_1' => array(
                // Current section
                'people',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'organisationPersons' => array()
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // People complete
            'people_2' => array(
                // Current section
                'people',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'organisation'=> array(
                            'organisationPersons' => array(
                                'foo'
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Taxi PHV incomplete
            'taxi_phv_1' => array(
                // Current section
                'taxi_phv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'privateHireLicences'=> array(
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Taxi PHV complete
            'taxi_phv_2' => array(
                // Current section
                'taxi_phv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'privateHireLicences'=> array(
                            'foo'
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 1
            'opereating_centres_1' => array(
                // Current section
                'operating_centres',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'operatingCentres' => array()
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 2
            'opereating_centres_2' => array(
                // Current section
                'operating_centres',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'totAuthVehicles' => null,
                    'totAuthTrailers' => null,
                    'totCommunityLicences' => null,
                    'operatingCentres' => array(
                        'foo'
                    ),
                    'licence' => array(
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 3
            'opereating_centres_3' => array(
                // Current section
                'operating_centres',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'totAuthVehicles' => null,
                    'totAuthTrailers' => null,
                    'totCommunityLicences' => null,
                    'operatingCentres' => array(
                        'foo'
                    ),
                    'licence' => array(
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        ),
                        'licenceType' => array(
                            'id' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 4
            'opereating_centres_4' => array(
                // Current section
                'operating_centres',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'totAuthVehicles' => null,
                    'totAuthTrailers' => null,
                    'totCommunityLicences' => null,
                    'operatingCentres' => array(
                        'foo'
                    ),
                    'licence' => array(
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        ),
                        'licenceType' => array(
                            'id' => LicenceEntityService::LICENCE_TYPE_RESTRICTED
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC complete 1
            'opereating_centres_5' => array(
                // Current section
                'operating_centres',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'totAuthVehicles' => 1,
                    'totAuthTrailers' => 1,
                    'totCommunityLicences' => null,
                    'operatingCentres' => array(
                        'foo'
                    ),
                    'licence' => array(
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial Evidence placeholder
            'financial_evidence_1' => array(
                // Current section
                'financial_evidence',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Transport managers placeholder
            'transport_managers_1' => array(
                // Current section
                'transport_managers',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            'vehicles_1' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthVehicles' => null
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            'vehicles_2' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceVehicles' => array(

                        )
                    ),
                    'totAuthVehicles' => null
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            'vehicles_3' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'foo'
                            )
                        )
                    ),
                    'totAuthVehicles' => null
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete (Too many vehicles)
            'vehicles_4' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'foo'
                            ),
                            array(
                                'foo'
                            )
                        )
                    ),
                    'totAuthVehicles' => 1
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles complete
            'vehicles_5' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'foo'
                            ),
                            array(
                                'foo'
                            )
                        )
                    ),
                    'totAuthVehicles' => 2
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles complete
            'vehicles_6' => array(
                // Current section
                'vehicles',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'foo'
                            ),
                            array(
                                'foo'
                            )
                        )
                    ),
                    'totAuthVehicles' => 4
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            'vehicles_declarations_1' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => null,
                    'psvOperateSmallVhl' => null,
                    'psvSmallVhlNotes' => null,
                    'psvSmallVhlConfirmation' => null,
                    'psvLimousines' => null,
                    'psvNoLimousineConfirmation' => null,
                    'psvOnlyLimousinesConfirmation' => null,
                    'licence' => array(
                        'trafficArea' => array(

                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            'vehicles_declarations_2' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 1,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => null,
                    'psvOperateSmallVhl' => null,
                    'psvSmallVhlNotes' => null,
                    'psvSmallVhlConfirmation' => null,
                    'psvLimousines' => null,
                    'psvNoLimousineConfirmation' => null,
                    'psvOnlyLimousinesConfirmation' => null,
                    'licence' => array(
                        'trafficArea' => array(

                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            'vehicles_declarations_3' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 1,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => null,
                    'psvOperateSmallVhl' => null,
                    'psvSmallVhlNotes' => null,
                    'psvSmallVhlConfirmation' => null,
                    'psvLimousines' => null,
                    'psvNoLimousineConfirmation' => null,
                    'psvOnlyLimousinesConfirmation' => null,
                    'licence' => array(
                        'trafficArea' => array(
                            'isScotland' => 1
                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            'vehicles_declarations_4' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 0,
                    'totAuthLargeVehicles' => 1,
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvOperateSmallVhl' => 'Y',
                    'psvSmallVhlNotes' => 'foo',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvLimousines' => 'N',
                    'psvNoLimousineConfirmation' => 'N',
                    'psvOnlyLimousinesConfirmation' => 'N',
                    'licence' => array(
                        'trafficArea' => array(
                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            'vehicles_declarations_5' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 1,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => null,
                    'psvOperateSmallVhl' => 'Y',
                    'psvSmallVhlNotes' => 'Y',
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvLimousines' => 'N',
                    'psvNoLimousineConfirmation' => 'N',
                    'psvOnlyLimousinesConfirmation' => 'N',
                    'licence' => array(
                        'trafficArea' => array(
                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            'vehicles_declarations_6' => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 1,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => null,
                    'psvOperateSmallVhl' => null,
                    'psvSmallVhlNotes' => null,
                    'psvSmallVhlConfirmation' => 'Y',
                    'psvLimousines' => 'N',
                    'psvNoLimousineConfirmation' => 'N',
                    'psvOnlyLimousinesConfirmation' => 'N',
                    'licence' => array(
                        'trafficArea' => array(
                            'isScotland' => 1
                        )
                    )

                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety incomplete
            'safety_1' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => null,
                    'licence' => array(
                        'safetyInsVehicles' => null,
                        'safetyInsVaries' => null,
                        'workshops' => array(),
                        'tachographIns' => array(
                            'id' => null
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety complete
            'safety_2' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => 'Y',
                    'licence' => array(
                        'safetyInsVehicles' => 1,
                        'safetyInsVaries' => 'Y',
                        'workshops' => array(
                            'foo'
                        ),
                        'tachographIns' => array(
                            'id' => 1
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety incomplete
            'safety_3' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => 'Y',
                    'licence' => array(
                        'tachographInsName' => null,
                        'safetyInsVehicles' => 1,
                        'safetyInsVaries' => 'Y',
                        'workshops' => array(
                            'foo'
                        ),
                        'tachographIns' => array(
                            'id' => 'tach_external'
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety complete
            'safety_4' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => 'Y',
                    'licence' => array(
                        'tachographInsName' => 'foo',
                        'safetyInsVehicles' => 1,
                        'safetyInsVaries' => 'Y',
                        'workshops' => array(
                            'foo'
                        ),
                        'tachographIns' => array(
                            'id' => 'tach_external'
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_PSV
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety incomplete
            'safety_5' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => 'Y',
                    'licence' => array(
                        'tachographInsName' => 'foo',
                        'safetyInsVehicles' => 1,
                        'safetyInsTrailers' => 0,
                        'safetyInsVaries' => 'Y',
                        'workshops' => array(
                            'foo'
                        ),
                        'tachographIns' => array(
                            'id' => 'tach_external'
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety complete
            'safety_6' => array(
                // Current section
                'safety',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'safetyConfirmation' => 'Y',
                    'licence' => array(
                        'tachographInsName' => 'foo',
                        'safetyInsVehicles' => 1,
                        'safetyInsTrailers' => 1,
                        'safetyInsVaries' => 'Y',
                        'workshops' => array(
                            'foo'
                        ),
                        'tachographIns' => array(
                            'id' => 'tach_external'
                        ),
                        'goodsOrPsv' => array(
                            'id' => LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history incomplete
            'financial_history_1' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => null,
                    'liquidation' => null,
                    'receivership' => null,
                    'administration' => null,
                    'disqualified' => null,
                    'insolvencyConfirmation' => null
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history incomplete
            'financial_history_2' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'N',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'N'
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history complete
            'financial_history_3' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'N',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'Y'
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history incomplete
            'financial_history_4' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'Y',
                    'insolvencyDetails' => ''
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history incomplete
            'financial_history_5' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'Y',
                    'insolvencyDetails' => str_repeat('a', 199)
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history complete
            'financial_history_6' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'Y',
                    'insolvencyDetails' => str_repeat('a', 200)
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial history complete
            'financial_history_7' => array(
                // Current section
                'financial_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'bankrupt' => 'Y',
                    'liquidation' => 'N',
                    'receivership' => 'N',
                    'administration' => 'N',
                    'disqualified' => 'N',
                    'insolvencyConfirmation' => 'Y',
                    'insolvencyDetails' => str_repeat('a', 300)
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Licence history incomplete
            'licence_history_1' => array(
                // Current section
                'licence_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevHasLicence' => null,
                    'prevHadLicence' => null,
                    'prevBeenRefused' => null,
                    'prevBeenRevoked' => null,
                    'prevBeenDisqualifiedTc' => null,
                    'prevBeenAtPi' => null,
                    'prevPurchasedAssets' => null,
                    'previousLicences' => array()
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Licence history incomplete
            'licence_history_2' => array(
                // Current section
                'licence_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevHasLicence' => 'Y',
                    'prevHadLicence' => null,
                    'prevBeenRefused' => null,
                    'prevBeenRevoked' => null,
                    'prevBeenDisqualifiedTc' => null,
                    'prevBeenAtPi' => null,
                    'prevPurchasedAssets' => null,
                    'previousLicences' => array()
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Licence history incomplete
            'licence_history_3' => array(
                // Current section
                'licence_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevHasLicence' => 'Y',
                    'prevHadLicence' => null,
                    'prevBeenRefused' => null,
                    'prevBeenRevoked' => null,
                    'prevBeenDisqualifiedTc' => null,
                    'prevBeenAtPi' => null,
                    'prevPurchasedAssets' => null,
                    'previousLicences' => array(
                        array(
                            'previousLicenceType' => array(
                                'id' => 'prev_had_licence'
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Licence history incomplete
            'licence_history_5' => array(
                // Current section
                'licence_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevHasLicence' => 'Y',
                    'prevHadLicence' => 'N',
                    'prevBeenRefused' => 'N',
                    'prevBeenRevoked' => 'N',
                    'prevBeenDisqualifiedTc' => 'N',
                    'prevBeenAtPi' => 'N',
                    'prevPurchasedAssets' => 'N',
                    'previousLicences' => array(
                        array(
                            'previousLicenceType' => array(
                                'id' => 'prev_had_licence'
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Licence history complete
            'licence_history_6' => array(
                // Current section
                'licence_history',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevHasLicence' => 'Y',
                    'prevHadLicence' => 'N',
                    'prevBeenRefused' => 'N',
                    'prevBeenRevoked' => 'N',
                    'prevBeenDisqualifiedTc' => 'N',
                    'prevBeenAtPi' => 'N',
                    'prevPurchasedAssets' => 'N',
                    'previousLicences' => array(
                        array(
                            'previousLicenceType' => array(
                                'id' => 'prev_has_licence'
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Convictions Penalties incomplete
            'convictions_penalties_1' => array(
                // Current section
                'convictions_penalties',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevConviction' => null,
                    'convictionsConfirmation' => null,
                    'previousConvictions' => array()
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Convictions Penalties incomplete
            'convictions_penalties_2' => array(
                // Current section
                'convictions_penalties',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevConviction' => 'Y',
                    'convictionsConfirmation' => 'Y',
                    'previousConvictions' => array()
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Convictions Penalties complete
            'convictions_penalties_3' => array(
                // Current section
                'convictions_penalties',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevConviction' => 'Y',
                    'convictionsConfirmation' => 'Y',
                    'previousConvictions' => array(
                        'foo'
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Convictions Penalties incomplete
            'convictions_penalties_4' => array(
                // Current section
                'convictions_penalties',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'prevConviction' => 'Y',
                    'convictionsConfirmation' => 'N',
                    'previousConvictions' => array(
                        'foo'
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Conditions Undertakings
            'conditions_undertakings_1' => array(
                // Current section
                'conditions_undertakings',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles PSV incomplete: no licence vehicles
            'vehicles_psv_1' => array(
                // Current section
                'vehicles_psv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    // none
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles PSV incomplete: too many vehicles
            'vehicles_psv_2' => array(
                // Current section
                'vehicles_psv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 0,
                    'totAuthMediumVehicles' => 0,
                    'totAuthLargeVehicles' => 0,
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'psvType' => array(
                                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles PSV incomplete: enough vehicles to satisfy completion, but one total is NULL (not answered)
            'vehicles_psv_3' => array(
                // Current section
                'vehicles_psv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 1,
                    'totAuthLargeVehicles' => null,
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                'psvType' => array(
                                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                                ),
                                'psvType' => array(
                                    'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_INCOMPLETE,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles PSV complete: enough vehicles to satisfy completion
            'vehicles_psv_4' => array(
                // Current section
                'vehicles_psv',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => 2,
                    'totAuthMediumVehicles' => 1,
                    'totAuthLargeVehicles' => 0,
                    'licence' => array(
                        'licenceVehicles' => array(
                            array(
                                // one small, but that's enough (we don't have to add them all)
                                'psvType' => array(
                                    'id' => VehicleEntityService::PSV_TYPE_SMALL
                                ),
                                'psvType' => array(
                                    'id' => VehicleEntityService::PSV_TYPE_MEDIUM
                                )
                            )
                        )
                    )
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Undertakings
            'undertakings_1' => array(
                // Current section
                'undertakings',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                ),
                // Expected completion data
                array(
                    'id' => 3,
                    'application' => 3,
                    'typeOfLicenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessTypeStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'businessDetailsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'addressesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'peopleStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'taxiPhvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'operatingCentresStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialEvidenceStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'transportManagersStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesPsvStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'vehiclesDeclarationsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'discsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'communityLicencesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'safetyStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'conditionsUndertakingsStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'financialHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'licenceHistoryStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED,
                    'undertakingsStatus' => ApplicationCompletionEntityService::STATUS_COMPLETE
                )
            ),
        );

        return $dataSets;
    }
}
