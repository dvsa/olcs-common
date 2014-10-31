<?php

/**
 * Application Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Entity;

use CommonTest\Bootstrap;
use PHPUnit_Framework_TestCase;
use Common\Service\Entity\ApplicationCompletionEntityService;
use Common\Service\Entity\OrganisationEntityService;
use Common\Service\Entity\ContactDetailsEntityService;
use Common\Service\Entity\LicenceEntityService;

/**
 * Application Completion Entity Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationCompletionEntityServiceTest extends PHPUnit_Framework_TestCase
{
    protected $sut;

    protected $sm;

    protected $restHelper;

    protected function setUp()
    {
        $this->sut = new ApplicationCompletionEntityService();

        $this->restHelper = $this->getMock('\stdClass', array('makeRestCall'));

        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);
        $this->sm->setService('Helper\Rest', $this->restHelper);

        $this->sut->setServiceLocator($this->sm);
    }

    protected function expectOneRestCall($entity, $method, $data, $bundle = null)
    {
        return $this->restHelper->expects($this->once())
            ->method('makeRestCall')
            ->with($entity, $method, $data, $bundle);
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
        $currentSection, $completionData, $applicationData, $expectedCompletionStatusData
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
            'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
        );

        return array(
            // Incomplete type of licence data
            0 => array(
                // Current section
                'type_of_licence',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'niFlag' => null,
                        'goodsOrPsv' => array(
                            'id' => 1
                        ),
                        'licenceType' => array(
                            'id' => 1
                        )
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Complete type of licence data
            1 => array(
                // Current section
                'type_of_licence',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'licence' => array(
                        'niFlag' => 'Y',
                        'goodsOrPsv' => array(
                            'id' => 1
                        ),
                        'licenceType' => array(
                            'id' => 1
                        )
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Missing business type data
            2 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Complete business type data
            3 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details not started
            4 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details incomplete llp
            5 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete llp
            6 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details incomplete partnership
            7 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete partnership
            8 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Business details complete sole trader
            9 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses incomplete 1
            10 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses complete 1
            11 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Addresses complete 2
            12 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // People incomplete
            13 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // People complete
            14 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Taxi PHV incomplete
            15 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Taxi PHV complete
            16 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 1
            17 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 2
            18 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 3
            19 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC incomplete 4
            20 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // OC complete 1
            21 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Financial Evidence placeholder
            22 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Transport managers placeholder
            23 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            24 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            25 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete
            26 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles incomplete (Too many vehicles)
            27 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles complete
            28 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles complete
            29 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            30 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            31 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations incomplete
            32 => array(
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
                            'isScottishRules' => 1
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            33 => array(
                // Current section
                'vehicles_declarations',
                // Mocked start completion data
                $initialStatus,
                // Mocked application data
                array(
                    'totAuthSmallVehicles' => null,
                    'totAuthMediumVehicles' => null,
                    'totAuthLargeVehicles' => null,
                    'psvNoSmallVhlConfirmation' => 'Y',
                    'psvOperateSmallVhl' => null,
                    'psvSmallVhlNotes' => null,
                    'psvSmallVhlConfirmation' => null,
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            34 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Vehicles declarations complete
            35 => array(
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
                            'isScottishRules' => 1
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
            // Safety incomplete
            36 => array(
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
                    'convictionsPenaltiesStatus' => ApplicationCompletionEntityService::STATUS_NOT_STARTED
                )
            ),
        );
    }
}
