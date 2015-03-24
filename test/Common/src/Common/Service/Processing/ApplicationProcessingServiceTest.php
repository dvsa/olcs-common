<?php

/**
 * Application Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;
use Common\Service\Entity\ApplicationEntityService;
use Common\Service\Processing\ApplicationProcessingService;
use Common\Service\Entity\LicenceEntityService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\FeeTypeDataService;
use Common\Service\Data\CategoryDataService;
use Common\Service\Entity\FeeEntityService;
use Mockery as m;
use Common\Service\Processing\ApplicationSnapshotProcessingService;
use Common\Service\Entity\ApplicationTrackingEntityService as Tracking;
use Common\Service\Entity\ApplicationCompletionEntityService as Completion;
use Common\Service\Entity\CommunityLicEntityService as CommunityLic;

/**
 * Application Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationProcessingServiceTest extends MockeryTestCase
{
    use MockDateTrait;

    protected $sm;
    protected $sut;

    protected function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sm->setAllowOverride(true);

        $this->sut = new ApplicationProcessingService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * @group processing_services
     */
    public function testProcessGrantApplicationForGoods()
    {
        $id = 6;
        $licenceId = 2;
        $category = LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE;
        $licenceType = LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL;
        $date = '2012-01-01';

        $this->mockDate($date);

        $expectedGrantData = array(
            'status' => ApplicationEntityService::APPLICATION_STATUS_GRANTED,
            'grantedDate' => '2012-01-01'
        );

        $expectedLicGrantData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_GRANTED,
            'grantedDate' => '2012-01-01'
        );

        $expectedTaskData = array(
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
            'description' => 'Grant fee due',
            'actionDate' => '2012-01-01',
            'assignedToUser' => 1,
            'assignedToTeam' => 2,
            'isClosed' => 'N',
            'urgent' => 'N',
            'application' => $id,
            'licence' => $licenceId,
        );

        $expectedTypeOfLicenceData = array(
            'licenceType' => $licenceType,
            'niFlag' => 'N',
            'goodsOrPsv' => $category
        );

        $stubbedFeeType = array(
            'id' => 20,
            'description' => 'Foo bar baz',
            'fixedValue' => '10.00'
        );

        $expectedFeeData = array(
            'amount' => 10.00,
            'application' => $id,
            'licence' => $licenceId,
            'invoicedDate' => '2012-01-01',
            'feeType' => 20,
            'description' => 'Foo bar baz for application ' . $id,
            'feeStatus' => FeeEntityService::STATUS_OUTSTANDING,
            'task' => 5
        );

        // processGrantApplication
        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('getCategory')
            ->with($id)
            ->andReturn($category);

        // grantApplication
        $mockApplicationService->shouldReceive('forceUpdate')
            ->with($id, $expectedGrantData);

        // grantLicence
        $mockLicenceService = m::mock();
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with($licenceId, $expectedLicGrantData);

        // createGrantTask
        $mockUserService = m::mock();
        $mockUserService->shouldReceive('getCurrentUser')
            ->andReturn(['id' => 1, 'team' => ['id' => 2]]);

        $mockTaskService = m::mock();
        $mockTaskService->shouldReceive('save')
            ->with($expectedTaskData)
            ->andReturn(['id' => 5]);

        // getFeeTypeForApplication
        $mockApplicationService->shouldReceive('getTypeOfLicenceData')
            ->with($id)
            ->andReturn($expectedTypeOfLicenceData)
            ->shouldReceive('getApplicationDate')
            ->andReturn('2013-01-01');

        $mockFeeTypeDataService = m::mock();
        $mockFeeTypeDataService->shouldReceive('getLatest')
            ->with(
                FeeTypeDataService::FEE_TYPE_GRANT,
                $category,
                $licenceType,
                '2013-01-01',
                false
            )
            ->andReturn($stubbedFeeType);

        // createGrantFee
        $mockFeeService = m::mock();
        $mockFeeService->shouldReceive('save')
            ->with($expectedFeeData);

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\User', $mockUserService);
        $this->sm->setService('Entity\Task', $mockTaskService);
        $this->sm->setService('Data\FeeType', $mockFeeTypeDataService);
        $this->sm->setService('Entity\Fee', $mockFeeService);

        $this->sut->processGrantApplication($id);
    }

    /**
     * @group processing_services
     */
    public function testProcessGrantApplicationForPsv()
    {
        $id = 6;
        $licenceId = 2;
        $category = LicenceEntityService::LICENCE_CATEGORY_PSV;
        $licenceType = LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL;
        $date = '2012-01-01';

        $mockFeeService = m::mock();
        $mockGrantConditionUndertaking = m::mock();
        $mockGrantCommunityLicence = m::mock();
        $mockGrantTransportManager = m::mock();
        $mockPeople = m::mock();
        $mockSnapshot = m::mock();
        $mockLicence = m::mock();

        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Processing\GrantConditionUndertaking', $mockGrantConditionUndertaking);
        $this->sm->setService('Processing\GrantCommunityLicence', $mockGrantCommunityLicence);
        $this->sm->setService('Processing\GrantTransportManager', $mockGrantTransportManager);
        $this->sm->setService('Processing\GrantPeople', $mockPeople);
        $this->sm->setService('Processing\ApplicationSnapshot', $mockSnapshot);
        $this->sm->setService('Processing\Licence', $mockLicence);

        $mockFeeService->shouldReceive('cancelInterimForApplication')->with($id);
        $mockGrantConditionUndertaking->shouldReceive('grant')->with($id, $licenceId);
        $mockGrantCommunityLicence->shouldReceive('voidOrGrant')->with($licenceId);
        $mockGrantTransportManager->shouldReceive('grant')->with($id, $licenceId);
        $mockPeople->shouldReceive('grant')->with($id);
        $mockSnapshot->shouldReceive('storeSnapshot')->with($id, ApplicationSnapshotProcessingService::ON_GRANT);
        $mockLicence->shouldReceive('generateDocument')->with($licenceId);

        $this->mockDate($date);

        $expectedGrantData = array(
            'status' => ApplicationEntityService::APPLICATION_STATUS_VALID,
            'grantedDate' => '2012-01-01'
        );

        $expectedLicGrantData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'grantedDate' => '2012-01-01'
        );

        $stubbedApplicationValidatingData = array(
            'totAuthTrailers' => null,
            'totAuthVehicles' => null,
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null,
            'licenceType' => $licenceType,
            'goodsOrPsv' => $category,
            'niFlag' => 'N'
        );

        $expectedLicenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'inForceDate' => '2012-01-01',
            'reviewDate' => '2017-01-01',
            'expiryDate' => '2016-12-31',
            'feeDate' => '2016-12-31',
            'totAuthTrailers' => null,
            'totAuthVehicles' => null,
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null,
            'licenceType' => $licenceType,
            'goodsOrPsv' => $category,
            'niFlag' => 'N'
        );

        $stubbedApplicationOperatingCentres = array(
            array(
                'action' => 'A',
                'operatingCentre' => array(
                    'id' => 7
                ),
                'isInterim' => false,
            ),
            array(
                'action' => 'D',
                'operatingCentre' => array(
                    'id' => 6
                ),
                'isInterim' => false
            )
        );

        $stubbedLicenceVehicles = array(
            array(
                'foo' => 'bar',
                'specifiedDate' => null,
            ),
            array(
                'foo' => 'bar',
                'specifiedDate' => '2010-10-10'
            )
        );

        $stubbedAppData = array(
            'totAuthSmallVehicles' => 2,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null
        );

        $stubbedLicData = array(
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null
        );

        // processGrantApplication
        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('getCategory')
            ->with($id)
            ->andReturn($category);

        // grantApplication
        $mockApplicationService->shouldReceive('forceUpdate')
            ->with($id, $expectedGrantData);

        // grantLicence
        $mockLicenceService = m::mock();
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with($licenceId, $expectedLicGrantData);

        // getApplicationDataForValidating
        $mockApplicationService->shouldReceive('getDataForValidating')
            ->with($id)
            ->andReturn($stubbedApplicationValidatingData);

        // copyApplicationDataToLicence
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with(
                $licenceId,
                $expectedLicenceData
            );

        // processApplicationOperatingCentres (Non-Special restricted only)
        $mockApplicationOperatingCentreService = m::mock();
        $mockApplicationOperatingCentreService->shouldReceive('getForApplication')
            ->with($id)
            ->andReturn($stubbedApplicationOperatingCentres);

        $mockLicenceOperatingCentreService = m::mock();
        $mockLicenceOperatingCentreService->shouldReceive('save')
            ->with(
                array(
                    'operatingCentre' => 7,
                    'licence' => $licenceId
                )
            )
            ->shouldReceive('deleteList')
            ->with(['operatingCentre' => 6]);

        // createDiscRecords
        $mockApplicationService->shouldReceive('getById')
            ->with($id)
            ->andReturn($stubbedAppData);

        $mockLicenceService->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn($stubbedLicData);

        $mockLicenceVehicleService = m::mock();
        $mockLicenceVehicleService->shouldReceive('getForApplicationValidation')
            ->with($licenceId, $id)
            ->andReturn($stubbedLicenceVehicles)
            ->shouldReceive('multiUpdate')
            ->with(
                array(
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01',
                        'interimApplication' => null
                    ),
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2010-10-10',
                        'interimApplication' => null
                    )
                )
            );

        // createPsvDiscs
        $mockPsvDiscService = m::mock();
        $mockPsvDiscService->shouldReceive('requestBlankDiscs')
            ->with($licenceId, 2);

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockApplicationOperatingCentreService);
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLicenceOperatingCentreService);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicleService);
        $this->sm->setService('Entity\PsvDisc', $mockPsvDiscService);

        $this->sut->processGrantApplication($id);
    }

    /**
     * @group processing_services
     */
    public function testProcessGrantApplicationForSpecialRestrictedPsv()
    {
        $id = 6;
        $licenceId = 2;
        $category = LicenceEntityService::LICENCE_CATEGORY_PSV;
        $licenceType = LicenceEntityService::LICENCE_TYPE_SPECIAL_RESTRICTED;
        $date = '2012-01-01';

        $mockFeeService = m::mock();
        $mockGrantConditionUndertaking = m::mock();
        $mockGrantCommunityLicence = m::mock();
        $mockGrantTransportManager = m::mock();
        $mockPeople = m::mock();
        $mockSnapshot = m::mock();
        $mockLicence = m::mock();

        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Processing\GrantConditionUndertaking', $mockGrantConditionUndertaking);
        $this->sm->setService('Processing\GrantCommunityLicence', $mockGrantCommunityLicence);
        $this->sm->setService('Processing\GrantTransportManager', $mockGrantTransportManager);
        $this->sm->setService('Processing\GrantPeople', $mockPeople);
        $this->sm->setService('Processing\ApplicationSnapshot', $mockSnapshot);
        $this->sm->setService('Processing\Licence', $mockLicence);

        $mockFeeService->shouldReceive('cancelInterimForApplication')->with($id);
        $mockGrantConditionUndertaking->shouldReceive('grant')->with($id, $licenceId);
        $mockGrantCommunityLicence->shouldReceive('voidOrGrant')->with($licenceId);
        $mockGrantTransportManager->shouldReceive('grant')->with($id, $licenceId);
        $mockPeople->shouldReceive('grant')->with($id);
        $mockSnapshot->shouldReceive('storeSnapshot')->with($id, ApplicationSnapshotProcessingService::ON_GRANT);
        $mockLicence->shouldReceive('generateDocument')->with($licenceId);

        $this->mockDate($date);

        $expectedGrantData = array(
            'status' => ApplicationEntityService::APPLICATION_STATUS_VALID,
            'grantedDate' => '2012-01-01'
        );

        $expectedLicGrantData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'grantedDate' => '2012-01-01'
        );

        $stubbedApplicationValidatingData = array(
            'totAuthTrailers' => null,
            'totAuthVehicles' => null,
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null,
            'licenceType' => $licenceType,
            'goodsOrPsv' => $category,
            'niFlag' => 'N'
        );

        $expectedLicenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'inForceDate' => '2012-01-01',
            'reviewDate' => '2017-01-01',
            'expiryDate' => '2016-12-31',
            'feeDate' => '2016-12-31',
            'totAuthTrailers' => null,
            'totAuthVehicles' => null,
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null,
            'licenceType' => $licenceType,
            'goodsOrPsv' => $category,
            'niFlag' => 'N'
        );

        $stubbedLicenceVehicles = array(
            array(
                'foo' => 'bar',
                'specifiedDate' => null,
            ),
            array(
                'foo' => 'bar',
                'specifiedDate' => '2010-10-10'
            )
        );

        $stubbedAppData = array(
            'totAuthSmallVehicles' => 2,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null
        );

        $stubbedLicData = array(
            'totAuthSmallVehicles' => null,
            'totAuthMediumVehicles' => null,
            'totAuthLargeVehicles' => null
        );

        // processGrantApplication
        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('getCategory')
            ->with($id)
            ->andReturn($category);

        // grantApplication
        $mockApplicationService->shouldReceive('forceUpdate')
            ->with($id, $expectedGrantData);

        // grantLicence
        $mockLicenceService = m::mock();
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with($licenceId, $expectedLicGrantData);

        // getApplicationDataForValidating
        $mockApplicationService->shouldReceive('getDataForValidating')
            ->with($id)
            ->andReturn($stubbedApplicationValidatingData);

        // copyApplicationDataToLicence
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with(
                $licenceId,
                $expectedLicenceData
            );

        // createDiscRecords
        $mockApplicationService->shouldReceive('getById')
            ->with($id)
            ->andReturn($stubbedAppData);

        $mockLicenceService->shouldReceive('getById')
            ->with($licenceId)
            ->andReturn($stubbedLicData);

        $mockLicenceVehicleService = m::mock();
        $mockLicenceVehicleService->shouldReceive('getForApplicationValidation')
            ->with($licenceId, $id)
            ->andReturn($stubbedLicenceVehicles)
            ->shouldReceive('multiUpdate')
            ->with(
                array(
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01',
                        'interimApplication' => null
                    ),
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2010-10-10',
                        'interimApplication' => null
                    )
                )
            );

        // createPsvDiscs
        $mockPsvDiscService = m::mock();
        $mockPsvDiscService->shouldReceive('requestBlankDiscs')
            ->with($licenceId, 2);

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicleService);
        $this->sm->setService('Entity\PsvDisc', $mockPsvDiscService);

        $this->sut->processGrantApplication($id);
    }

    /**
     * @group processing_services
     */
    public function testProcessUnGrantApplication()
    {
        $id = 3;
        $licenceId = 6;

        $expectedGrantData = array(
            'status' => ApplicationEntityService::APPLICATION_STATUS_UNDER_CONSIDERATION,
            'grantedDate' => null
        );

        $expectedLicGrantData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_UNDER_CONSIDERATION,
            'grantedDate' => null
        );

        $expectedTaskQuery = array(
            'category' => CategoryDataService::CATEGORY_APPLICATION,
            'subCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
            'licence' => $licenceId,
            'application' => $id
        );

        // processUnGrantApplication
        $mockApplicationService = m::mock();
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId);

        // undoGrantApplication
        $mockApplicationService->shouldReceive('forceUpdate')
            ->with($id, $expectedGrantData);

        // undoGrantLicence
        $mockLicenceService = m::mock();
        $mockLicenceService->shouldReceive('forceUpdate')
            ->with($licenceId, $expectedLicGrantData);

        // cancelFees
        $mockFeeService = m::mock();
        $mockFeeService->shouldReceive('cancelForLicence')
            ->with($licenceId);

        // closeGrantTask
        $mockTaskService = m::mock();
        $mockTaskService->shouldReceive('closeByQuery')
            ->with($expectedTaskQuery);

        $this->sm->setService('Entity\Application', $mockApplicationService);
        $this->sm->setService('Entity\Licence', $mockLicenceService);
        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Entity\Task', $mockTaskService);

        $this->sut->processUnGrantApplication($id);
    }

    /**
     * @group processing_services
     */
    public function testValidateApplicationWithoutLicenceVehicles()
    {
        $id = 3;
        $licenceId = 6;
        $date = '2014-06-12';
        $this->mockDate($date);
        $validationData = array(
            'totAuthTrailers' => 6,
            'totAuthVehicles' => 9,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
        );

        $expectedLicenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'totAuthTrailers' => 6,
            'totAuthVehicles' => 9,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            'inForceDate' => '2014-06-12',
            'reviewDate' => '2019-06-12',
            'expiryDate' => '2019-05-31',
            'feeDate' => '2019-05-31'
        );

        $mockFeeService = m::mock();
        $mockGrantConditionUndertaking = m::mock();
        $mockGrantCommunityLicence = m::mock();
        $mockGrantTransportManager = m::mock();
        $mockPeople = m::mock();
        $mockSnapshot = m::mock();
        $mockLicence = m::mock();

        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Processing\GrantConditionUndertaking', $mockGrantConditionUndertaking);
        $this->sm->setService('Processing\GrantCommunityLicence', $mockGrantCommunityLicence);
        $this->sm->setService('Processing\GrantTransportManager', $mockGrantTransportManager);
        $this->sm->setService('Processing\GrantPeople', $mockPeople);
        $this->sm->setService('Processing\ApplicationSnapshot', $mockSnapshot);
        $this->sm->setService('Processing\Licence', $mockLicence);

        $mockFeeService->shouldReceive('cancelInterimForApplication')->with($id);
        $mockGrantConditionUndertaking->shouldReceive('grant')->with($id, $licenceId);
        $mockGrantCommunityLicence->shouldReceive('voidOrGrant')->with($licenceId);
        $mockGrantTransportManager->shouldReceive('grant')->with($id, $licenceId);
        $mockPeople->shouldReceive('grant')->with($id);
        $mockSnapshot->shouldReceive('storeSnapshot')->with($id, ApplicationSnapshotProcessingService::ON_GRANT);
        $mockLicence->shouldReceive('generateDocument')->with($licenceId);

        $this->mockApplicationService($id, $licenceId, $validationData);

        $this->mockLicenceService($licenceId, $expectedLicenceData);

        $this->mockApplicationOperatingCentre($id);

        $this->mockLicenceOperatingCentre($licenceId);

        $mockLicenceVehicles = array();
        $this->mockLicenceVehicles($licenceId, $mockLicenceVehicles);

        $this->expectSuccessMessage();

        $this->sut->validateApplication($id);
    }

    /**
     * @group processing_services
     */
    public function testValidateApplicationForGoodsApplication()
    {
        $id = 3;
        $licenceId = 6;
        $date = '2014-06-12';
        $this->mockDate($date);
        $validationData = array(
            'totAuthTrailers' => 6,
            'totAuthVehicles' => 9,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL
        );

        $expectedLicenceData = array(
            'status' => LicenceEntityService::LICENCE_STATUS_VALID,
            'totAuthTrailers' => 6,
            'totAuthVehicles' => 9,
            'totAuthSmallVehicles' => 3,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles' => 3,
            'licenceType' => LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
            'inForceDate' => '2014-06-12',
            'reviewDate' => '2019-06-12',
            'expiryDate' => '2019-05-31',
            'feeDate' => '2019-05-31'
        );

        $mockFeeService = m::mock();
        $mockGrantConditionUndertaking = m::mock();
        $mockGrantCommunityLicence = m::mock();
        $mockGrantTransportManager = m::mock();
        $mockPeople = m::mock();
        $mockSnapshot = m::mock();
        $mockLicence = m::mock();

        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Processing\GrantConditionUndertaking', $mockGrantConditionUndertaking);
        $this->sm->setService('Processing\GrantCommunityLicence', $mockGrantCommunityLicence);
        $this->sm->setService('Processing\GrantTransportManager', $mockGrantTransportManager);
        $this->sm->setService('Processing\GrantPeople', $mockPeople);
        $this->sm->setService('Processing\ApplicationSnapshot', $mockSnapshot);
        $this->sm->setService('Processing\Licence', $mockLicence);

        $mockFeeService->shouldReceive('cancelInterimForApplication')->with($id);
        $mockGrantConditionUndertaking->shouldReceive('grant')->with($id, $licenceId);
        $mockGrantCommunityLicence->shouldReceive('voidOrGrant')->with($licenceId);
        $mockGrantTransportManager->shouldReceive('grant')->with($id, $licenceId);
        $mockPeople->shouldReceive('grant')->with($id);
        $mockSnapshot->shouldReceive('storeSnapshot')->with($id, ApplicationSnapshotProcessingService::ON_GRANT);
        $mockLicence->shouldReceive('generateDocument')->with($licenceId);

        $mockApplicationService = $this->mockApplicationService($id, $licenceId, $validationData);

        $this->mockLicenceService($licenceId, $expectedLicenceData);

        $this->mockApplicationOperatingCentre($id);

        $this->mockLicenceOperatingCentre($licenceId);

        $mockLicenceVehicles = array(
            array(
                'id' => 1,
                'specifiedDate' => null
            ),
            array(
                'id' => 2,
                'specifiedDate' => null
            )
        );
        $mockLicenceVehicleService = $this->mockLicenceVehicles($licenceId, $mockLicenceVehicles);

        $mockLicenceVehicleService->expects($this->once())
            ->method('multiUpdate')
            ->with(
                array(
                    array(
                        'id' => 1,
                        'specifiedDate' => $date,
                        'interimApplication' => null
                    ),
                    array(
                        'id' => 2,
                        'specifiedDate' => $date,
                        'interimApplication' => null
                    )
                )
            );

        $mockApplicationService->expects($this->once())
            ->method('getCategory')
            ->with($id)
            ->will($this->returnValue(LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE));

        $goodsDiscMock = m::mock()
            ->shouldReceive('createForVehicles')
            ->once()
            ->with($mockLicenceVehicles)
            ->getMock();

        $this->sm->setService('Entity\GoodsDisc', $goodsDiscMock);

        $this->expectSuccessMessage();

        $this->sut->validateApplication($id);
    }

    public function testMaybeCreateVariationFeeNoExistingFee()
    {
        $applicationId = 123;
        $licenceId     = 456;

        // we'll mock the 'createFee' method as it's tested elsewhere
        $this->sut = m::mock('Common\Service\Processing\ApplicationProcessingService')
            ->makePartial()->shouldAllowMockingProtectedMethods();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestOutstandingFeeForApplication')
                ->once()
                ->with($applicationId)
                ->andReturn(null)
                ->getMock()
        );

        $this->sut->shouldReceive('createFee')->once()->with(
            $applicationId,
            $licenceId,
            'VAR'
        );

        $this->sut->maybeCreateVariationFee($applicationId, $licenceId);
    }

    public function testMaybeCreateVariationFeeWithExistingFee()
    {
        $applicationId = 123;
        $licenceId     = 456;

        // we'll mock the 'createFee' method as it's tested elsewhere
        $this->sut = m::mock('Common\Service\Processing\ApplicationProcessingService')
            ->makePartial()->shouldAllowMockingProtectedMethods();
        $this->sut->setServiceLocator($this->sm);

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestOutstandingFeeForApplication')
                ->once()
                ->with($applicationId)
                ->andReturn(['id' => 99, 'amount' => '99.99'])
                ->getMock()
        );

        $this->sut->shouldReceive('createFee')->never();

        $this->sut->maybeCreateVariationFee($applicationId, $licenceId);
    }

    public function testMaybeCancelVariationFeeWithExistingFee()
    {
        $applicationId = 123;

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestOutstandingFeeForApplication')
                    ->once()
                    ->with($applicationId)
                    ->andReturn(['id' => 99, 'amount' => '99.99'])
                ->shouldReceive('cancelForApplication')
                    ->once()
                    ->with($applicationId)
                ->getMock()
        );

        $this->sut->maybeCancelVariationFee($applicationId);
    }

    public function testMaybeCancelVariationFeeNoExistingFee()
    {
        $applicationId = 123;

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestOutstandingFeeForApplication')
                    ->once()
                    ->with($applicationId)
                    ->andReturn(null)
                ->shouldReceive('cancelForApplication')
                    ->never()
                ->getMock()
        );

        $this->sut->maybeCancelVariationFee($applicationId);
    }

    protected function mockApplicationService($id, $licenceId, $validationData)
    {
        $mockApplicationService = $this->getMock(
            '\stdClass',
            ['getLicenceIdForApplication', 'forceUpdate', 'getDataForValidating', 'getCategory']
        );
        $mockApplicationService->expects($this->once())
            ->method('getLicenceIdForApplication')
            ->with($id)
            ->will($this->returnValue($licenceId));

        $appStatusData = array('status' => ApplicationEntityService::APPLICATION_STATUS_VALID);

        $mockApplicationService->expects($this->once())
            ->method('forceUpdate')
            ->with($id, $appStatusData);
        $mockApplicationService->expects($this->once())
            ->method('getDataForValidating')
            ->with($id)
            ->will($this->returnValue($validationData));
        $this->sm->setService('Entity\Application', $mockApplicationService);

        return $mockApplicationService;
    }

    protected function mockLicenceService($licenceId, $expectedLicenceData)
    {
        $mockLicenceService = $this->getMock('\stdClass', ['forceUpdate']);
        $mockLicenceService->expects($this->once())
            ->method('forceUpdate')
            ->with($licenceId, $expectedLicenceData);
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        return $mockLicenceService;
    }

    protected function mockApplicationOperatingCentre($id)
    {
        $aocData = array(
            array(
                'action' => 'A',
                'id' => 10,
                'operatingCentre' => array(
                    'id' => 4
                ),
                'isInterim' => false
            ),
            array(
                'action' => 'A',
                'id' => 12,
                'operatingCentre' => array(
                    'id' => 5
                ),
                'isInterim' => true
            )
        );

        $mockApplicationOperatingCentre = $this->getMock('\stdClass', ['getForApplication', 'clearInterims']);
        $mockApplicationOperatingCentre->expects($this->once())
            ->method('getForApplication')
            ->with($id)
            ->will($this->returnValue($aocData));

        $mockApplicationOperatingCentre->expects($this->once())
            ->method('clearInterims')
            ->with([12]);

        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockApplicationOperatingCentre);
    }

    protected function mockLicenceOperatingCentre($licenceId)
    {
        $mockLicenceOperatingCentre = $this->getMock('\stdClass', ['save']);
        $mockLicenceOperatingCentre->expects($this->at(0))
            ->method('save')
            ->with(
                array(
                    'operatingCentre' => 4,
                    'licence' => $licenceId
                )
            );
        $mockLicenceOperatingCentre->expects($this->at(1))
            ->method('save')
            ->with(
                array(
                    'operatingCentre' => 5,
                    'licence' => $licenceId
                )
            );
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLicenceOperatingCentre);
    }

    protected function mockLicenceVehicles($licenceId, $mockLicenceVehicles)
    {
        $mockLicenceVehicle = $this->getMock('\stdClass', ['getForApplicationValidation', 'multiUpdate']);
        $mockLicenceVehicle->expects($this->once())
            ->method('getForApplicationValidation')
            ->with($licenceId)
            ->will($this->returnValue($mockLicenceVehicles));
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        return $mockLicenceVehicle;
    }

    protected function expectSuccessMessage()
    {
        $mockFlashMessenger = $this->getMock('\stdClass', ['addSuccessMessage']);
        $mockFlashMessenger->expects($this->once())
            ->method('addSuccessMessage')
            ->with('licence-valid-confirmation');
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);
    }

    /**
     * @dataProvider variationLicenceTypesProvider
     */
    public function testProcessGrantVariation($licenceType, $applicationType, $category, $expectation)
    {
        // Params
        $id = 2;

        // Stubbed data
        $licenceId = 5;
        $this->mockDate('2014-01-01');
        $stubbedValidatingData = [
            'foo' => 'bar'
        ];
        $stubbedAoc = [
            [
                'id' => 5,
                'action' => 'U',
                'foo' => 'bar',
                'operatingCentre' => [
                    'id' => 6
                ],
                'isInterim' => false
            ]
        ];
        $stubbedLoc = [
            'Results' => [
                [
                    'id' => 6,
                    'operatingCentre' => [
                        'id' => 7
                    ]
                ],
                [
                    'id' => 5,
                    'operatingCentre' => [
                        'id' => 6
                    ]
                ]
            ]
        ];
        $expectedLocData = [
            'foo' => 'bar',
            'operatingCentre' => 6,
            'licence' => 5
        ];

        $mockFeeService = m::mock();
        $mockGrantConditionUndertaking = m::mock();
        $mockGrantCommunityLicence = m::mock();
        $mockGrantTransportManager = m::mock();
        $mockPeople = m::mock();
        $mockSnapshot = m::mock();
        $mockLicence = m::mock();

        $this->sm->setService('Entity\Fee', $mockFeeService);
        $this->sm->setService('Processing\GrantConditionUndertaking', $mockGrantConditionUndertaking);
        $this->sm->setService('Processing\GrantCommunityLicence', $mockGrantCommunityLicence);
        $this->sm->setService('Processing\GrantTransportManager', $mockGrantTransportManager);
        $this->sm->setService('Processing\GrantPeople', $mockPeople);
        $this->sm->setService('Processing\ApplicationSnapshot', $mockSnapshot);
        $this->sm->setService('Processing\Licence', $mockLicence);

        $mockFeeService->shouldReceive('cancelInterimForApplication')->with($id);
        $mockGrantConditionUndertaking->shouldReceive('grant')->with($id, $licenceId);
        $mockGrantCommunityLicence->shouldReceive('voidOrGrant')->with($licenceId);
        $mockGrantTransportManager->shouldReceive('grant')->with($id, $licenceId);
        $mockPeople->shouldReceive('grant')->with($id);
        $mockSnapshot->shouldReceive('storeSnapshot')->with($id, ApplicationSnapshotProcessingService::ON_GRANT);
        $mockLicence->shouldReceive('generateDocument')->with($licenceId);

        // Mocked services
        $mockApplicationService = m::mock();
        $this->sm->setService('Entity\Application', $mockApplicationService);

        $mockLicenceService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceService);

        $mockLicenceVehicleService = m::mock();
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicleService);

        $mockAocService = m::mock();
        $this->sm->setService('Entity\ApplicationOperatingCentre', $mockAocService);

        $mockLocService = m::mock();
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLocService);

        $mockGoodsService = m::mock();
        $this->sm->setService('Entity\GoodsDisc', $mockGoodsService);

        $mockPsvService = m::mock();
        $this->sm->setService('Entity\PsvDisc', $mockPsvService);

        // Expectations
        $mockApplicationService->shouldReceive('getLicenceIdForApplication')
            ->with($id)
            ->andReturn($licenceId)
            ->shouldReceive('forceUpdate')
            ->with(
                $id,
                ['status' => ApplicationEntityService::APPLICATION_STATUS_VALID, 'grantedDate' => '2014-01-01']
            )
            ->shouldReceive('getDataForValidating')
            ->with($id)
            ->andReturn($stubbedValidatingData)
            ->shouldReceive('getOverview')
            ->andReturn(
                [
                    'licenceType' => [
                        'id' => $applicationType,
                    ],
                    'goodsOrPsv' => [
                        'id' => $category
                    ]
                ]
            )
            ->shouldReceive('getById')
            ->andReturn(
                [
                    'totAuthLargeVehicles' => 0,
                    'totAuthMediumVehicles' => 0,
                    'totAuthSmallVehicles' => 0
                ]
            );

        // Create disc records
        $mockLicenceVehicleService->shouldReceive('getForApplicationValidation')
            ->with($licenceId, $id)
            ->andReturn([]);

        $mockLicenceService->shouldReceive('forceUpdate')
            ->with($licenceId, $stubbedValidatingData)
            ->shouldReceive('getOverview')
            ->andReturn(
                [
                    'licenceType' => [
                        'id' => $licenceType
                    ],
                    'goodsOrPsv' => [
                        'id' => $category
                    ]
                ]
            )
            ->shouldReceive('getById')
            ->andReturn(
                [
                    'totAuthLargeVehicles' => 0,
                    'totAuthMediumVehicles' => 0,
                    'totAuthSmallVehicles' => 0
                ]
            );

        $mockAocService->shouldReceive('getForApplication')
            ->with($id)
            ->andReturn($stubbedAoc);

        $mockLocService->shouldReceive('getListForLva')
            ->with($licenceId)
            ->andReturn($stubbedLoc)
            ->shouldReceive('forceUpdate')
            ->with(5, $expectedLocData);

        $expectation($mockGoodsService, $mockPsvService);

        $this->sut->processGrantVariation($id);
    }

    public function variationLicenceTypesProvider()
    {
        return [
            [
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                function ($goodsService, $psvService) {
                    $goodsService->shouldReceive('updateExistingForLicence')->never();
                    $psvService->shouldReceive('updateExistingForLicence')->never();
                }
            ], [
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE,
                function ($goodsService, $psvService) {
                    $goodsService->shouldReceive('updateExistingForLicence')
                        ->once()
                        ->with(5, 2);
                    $psvService->shouldReceive('updateExistingForLicence')->never();
                }
            ], [
                LicenceEntityService::LICENCE_TYPE_STANDARD_NATIONAL,
                LicenceEntityService::LICENCE_TYPE_STANDARD_INTERNATIONAL,
                LicenceEntityService::LICENCE_CATEGORY_PSV,
                function ($goodsService, $psvService) {
                    $goodsService->shouldReceive('updateExistingForLicence')->never();
                    $psvService->shouldReceive('updateExistingForLicence')
                        ->once()
                        ->with(5);
                }
            ]
        ];
    }

    /**
     * @dataProvider trackingIsValidProvider
     * @param array $requiredSections
     * @param array $completions
     * @param boolean $expected
     */
    public function testTrackingIsValid($requiredSections, $completions, $expected)
    {
        $applicationId = 123;

        $this->sm->setService(
            'Entity\ApplicationTracking',
            m::mock()
                ->shouldReceive('getTrackingStatuses')
                ->with($applicationId)
                ->andReturn($completions)
                ->getMock()
        );

        $this->assertEquals(
            $expected,
            $this->sut->trackingIsValid($applicationId, $requiredSections)
        );
    }

    public function trackingIsValidProvider()
    {
        return [
            'tracking complete' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => Tracking::STATUS_ACCEPTED,
                    'businessTypeStatus' => Tracking::STATUS_ACCEPTED,
                    'operatingCentresStatus' => Tracking::STATUS_NOT_APPLICABLE,
                ],
                true
            ],
            'tracking not started' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => null,
                    'businessTypeStatus' => null,
                    'operatingCentresStatus' => null,
                ],
                false
            ],
            'tracking not accepted' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => Tracking::STATUS_NOT_ACCEPTED,
                    'businessTypeStatus' => Tracking::STATUS_ACCEPTED,
                    'operatingCentresStatus' => Tracking::STATUS_NOT_APPLICABLE,
                ],
                false
            ],
        ];
    }

    public function testGetIncompleteSections()
    {
        $applicationId = 123;

        $requiredSections = [
            'type_of_licence',
            'business_type',
            'operating_centres',
        ];

        $this->sm->setService(
            'Entity\ApplicationCompletion',
            m::mock()
                ->shouldReceive('getCompletionStatuses')
                ->with($applicationId)
                ->andReturn(
                    [
                        'typeOfLicenceStatus' => Completion::STATUS_INCOMPLETE,
                        'businessTypeStatus' => Completion::STATUS_COMPLETE,
                        'operatingCentresStatus' => Completion::STATUS_INCOMPLETE,
                    ]
                )
                ->getMock()
        );

        $expected = ['type_of_licence', 'operating_centres'];

        $this->assertEquals(
            $expected,
            $this->sut->getIncompleteSections($applicationId, $requiredSections)
        );
    }

    /**
     * @dataProvider sectionCompletionIsValidProvider
     * @param array $requiredSections
     * @param array $completions
     * @param boolean $expected
     */
    public function testSectionCompletionIsValid($requiredSections, $completions, $expected)
    {
        $applicationId = 123;

        $this->sm->setService(
            'Entity\ApplicationCompletion',
            m::mock()
                ->shouldReceive('getCompletionStatuses')
                ->with($applicationId)
                ->andReturn($completions)
                ->getMock()
        );

        $this->assertEquals(
            $expected,
            $this->sut->sectionCompletionIsValid($applicationId, $requiredSections)
        );
    }

    public function sectionCompletionIsValidProvider()
    {
        return [
            'sections complete' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => Completion::STATUS_COMPLETE,
                    'businessTypeStatus' => Completion::STATUS_COMPLETE,
                    'operatingCentresStatus' => Completion::STATUS_COMPLETE,
                ],
                true
            ],
            'sections not complete' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => Completion::STATUS_COMPLETE,
                    'businessTypeStatus' => Completion::STATUS_INCOMPLETE,
                    'operatingCentresStatus' => Completion::STATUS_COMPLETE,
                ],
                false
            ],
            'missing completion data' => [
                [
                    'type_of_licence',
                    'business_type',
                    'operating_centres',
                ],
                [
                    'typeOfLicenceStatus' => Completion::STATUS_COMPLETE,
                    'businessTypeStatus' => Completion::STATUS_COMPLETE,
                ],
                false
            ],
        ];
    }

    public function testFeeStatusIsValidWithNoOutstandingFees()
    {
        $applicationId = 123;
        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getOutstandingFeesForApplication')
                ->with($applicationId)
                ->andReturn([])
                ->getMock()
        );
        $this->assertTrue($this->sut->feeStatusIsValid($applicationId));
    }

    public function testFeeStatusIsValidWithOutstandingInterimFees()
    {
        $applicationId = 123;
        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getOutstandingFeesForApplication')
                ->with($applicationId)
                ->andReturn(
                    [
                        [
                            'feeType' => [
                                'feeType' => FeeTypeDataService::FEE_TYPE_GRANTINT
                            ]
                        ]
                    ]
                )
                ->getMock()
        );
        $this->assertTrue($this->sut->feeStatusIsValid($applicationId));
    }

    public function testFeeStatusIsValidWithOutstandingNonInterimFees()
    {
        $applicationId = 123;
        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getOutstandingFeesForApplication')
                ->with($applicationId)
                ->andReturn(
                    [
                        [
                            'feeType' => [
                                'feeType' => FeeTypeDataService::FEE_TYPE_GRANT
                            ]
                        ]
                    ]
                )
                ->getMock()
        );
        $this->assertFalse($this->sut->feeStatusIsValid($applicationId));
    }

    /**
     * @dataProvider getApplicationFeeProvider
     * @param $applicationType
     * @param $expectedFeeType
     */
    public function testGetApplicationFee($applicationType, $expectedFeeType)
    {
        $applicationId = 69;

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getTypeOfLicenceData')
                    ->with($applicationId)
                    ->andReturn(
                        [
                            'licenceType' => 'ltyp_sn',
                            'niFlag'      => 'N',
                            'goodsOrPsv'  => 'lcat_gv',
                        ]
                    )
                ->shouldReceive('getApplicationDate')
                    ->with($applicationId)
                    ->andReturn('2015-03-10')
                ->shouldReceive('getApplicationType')
                    ->with($applicationId)
                    ->andReturn($applicationType)
                ->getMock()
        );

        $this->sm->setService(
            'Data\FeeType',
            m::mock()
                ->shouldReceive('getLatest')
                ->with(
                    $expectedFeeType,
                    'lcat_gv',
                    'ltyp_sn',
                    '2015-03-10',
                    null
                )
                ->andReturn(
                    [
                        'id' => 99,
                        'description' => 'Application Fee Type Description',
                        'fixedValue' => '100.00',
                    ]
                )
                ->getMock()
        );

        $fee = ['id' => 1];

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestFeeByTypeStatusesAndApplicationId')
                ->with(
                    99,
                    [FeeEntityService::STATUS_OUTSTANDING, FeeEntityService::STATUS_WAIVE_RECOMMENDED],
                    $applicationId
                )
                ->andReturn($fee)
                ->getMock()
        );

        $this->assertEquals($fee, $this->sut->getApplicationFee($applicationId));
    }

    public function getApplicationFeeProvider()
    {
        return [
            [ApplicationEntityService::APPLICATION_TYPE_VARIATION, FeeTypeDataService::FEE_TYPE_VAR],
            [ApplicationEntityService::APPLICATION_TYPE_NEW, FeeTypeDataService::FEE_TYPE_APP],
        ];
    }

    public function testGetInterimFee()
    {
        $applicationId = 69;

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getTypeOfLicenceData')
                    ->with($applicationId)
                    ->andReturn(
                        [
                            'licenceType' => 'ltyp_sn',
                            'niFlag'      => 'N',
                            'goodsOrPsv'  => 'lcat_gv',
                        ]
                    )
                ->shouldReceive('getApplicationDate')
                    ->with($applicationId)
                    ->andReturn('2015-03-10')
                ->getMock()
        );

        $this->sm->setService(
            'Data\FeeType',
            m::mock()
                ->shouldReceive('getLatest')
                ->with(
                    FeeTypeDataService::FEE_TYPE_GRANTINT,
                    'lcat_gv',
                    'ltyp_sn',
                    '2015-03-10',
                    null
                )
                ->andReturn(
                    [
                        'id' => 101,
                        'description' => 'Interim Fee Type Description',
                        'fixedValue' => '100.00',
                    ]
                )
                ->getMock()
        );

        $fee = ['id' => 1];

        $this->sm->setService(
            'Entity\Fee',
            m::mock()
                ->shouldReceive('getLatestFeeByTypeStatusesAndApplicationId')
                ->with(
                    101,
                    [FeeEntityService::STATUS_OUTSTANDING, FeeEntityService::STATUS_WAIVE_RECOMMENDED],
                    $applicationId
                )
                ->andReturn($fee)
                ->getMock()
        );

        $this->assertEquals($fee, $this->sut->getInterimFee($applicationId));
    }

    /**
     * @group processing_services
     */
    public function testProcessWithdrawApplication()
    {
        $applicationId = 69;
        $licenceId = 100;

        $this->sm->setService(
            'Helper\Date',
            m::mock()
                ->shouldReceive('getDate')
                ->andReturn('2015-03-18 00:00:00')
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('forceUpdate')
                    ->with(
                        $applicationId,
                        [
                            'status' => ApplicationEntityService::APPLICATION_STATUS_WITHDRAWN,
                            'withdrawnDate' => '2015-03-18 00:00:00',
                            'withdrawnReason' => 'REASON',
                        ]
                    )
                    ->once()
                ->shouldReceive('getApplicationType')
                    ->with($applicationId)
                    ->once()
                    ->andReturn(ApplicationEntityService::APPLICATION_TYPE_NEW)
                ->shouldReceive('getLicenceIdForApplication')
                    ->with($applicationId)
                    ->once()
                    ->andReturn($licenceId)
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('setLicenceStatus')
                    ->with($licenceId, LicenceEntityService::LICENCE_STATUS_WITHDRAWN)
                    ->once()
                ->getMock()
        );

        $this->sm->setService(
            'Helper\Interim',
            m::mock()
                ->shouldReceive('voidDiscsForApplication')
                    ->with($applicationId)
                    ->once()
                ->getMock()
        );

        $this->sut->processWithdrawApplication($applicationId, 'REASON');
    }

    /**
     * @group processing_services
     */
    public function testProcessRefuseApplication()
    {
        $applicationId = 69;
        $licenceId = 100;

        $this->sm->setService(
            'Helper\Date',
            m::mock()
                ->shouldReceive('getDate')
                ->andReturn('2015-03-19 00:00:00')
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('forceUpdate')
                    ->with(
                        $applicationId,
                        [
                            'status' => ApplicationEntityService::APPLICATION_STATUS_REFUSED,
                            'refusedDate' => '2015-03-19 00:00:00',
                        ]
                    )
                    ->once()
                ->shouldReceive('getApplicationType')
                    ->with($applicationId)
                    ->once()
                    ->andReturn(ApplicationEntityService::APPLICATION_TYPE_NEW)
                ->shouldReceive('getLicenceIdForApplication')
                    ->with($applicationId)
                    ->once()
                    ->andReturn($licenceId)
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('setLicenceStatus')
                    ->with($licenceId, LicenceEntityService::LICENCE_STATUS_REFUSED)
                    ->once()
                ->getMock()
        );

        $this->sm->setService(
            'Helper\Interim',
            m::mock()
                ->shouldReceive('voidDiscsForApplication')
                    ->with($applicationId)
                    ->once()
                ->getMock()
        );

        $this->sut->processRefuseApplication($applicationId);
    }

    /**
     * @group processing_services
     */
    public function testProcessNotTakenUpApplication()
    {
        $applicationId = 69;
        $licenceId = 100;

        $date = '2015-03-23 00:00:00';
        $this->mockDate($date);

        // mock service dependencies
        $mockApplicationEntityService = m::mock();
        $mockLicenceEntityService = m::mock();
        $mockGoodsDiscEntityService = m::mock();
        $mockLicenceVehicleEntityService = m::mock();
        $mockTmApplicationEntityService = m::mock();
        $mockCommunityLicEntityService = m::mock();

        $this->sm->setService('Entity\Application', $mockApplicationEntityService);
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);
        $this->sm->setService('Entity\GoodsDisc', $mockGoodsDiscEntityService);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicleEntityService);
        $this->sm->setService('Entity\TransportManagerApplication', $mockTmApplicationEntityService);
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicEntityService);

        // expectations
        $mockApplicationEntityService
            ->shouldReceive('forceUpdate')
                ->once()
                ->with(
                    $applicationId,
                    ['status' => ApplicationEntityService::APPLICATION_STATUS_NOT_TAKEN_UP]
                )
            ->shouldReceive('getLicenceIdForApplication')
                ->once()
                ->with($applicationId)
                ->andReturn($licenceId);

        $mockLicenceEntityService->shouldReceive('setLicenceStatus')
            ->once()
            ->with($licenceId, LicenceEntityService::LICENCE_STATUS_NOT_TAKEN_UP);

        $mockGoodsDiscEntityService->shouldReceive('voidExistingForApplication')
            ->once()
            ->with($applicationId);

        $mockLicenceVehicleEntityService->shouldReceive('removeForApplication')
            ->once()
            ->with($applicationId);

        $mockTmApplicationEntityService->shouldReceive('deleteForApplication')
            ->once()
            ->with($applicationId);

        $mockLicenceEntityService->shouldReceive('getCommunityLicencesByLicenceId')
            ->once()
            ->with($licenceId)
            ->andReturn(
                [
                    ['id' => 69],
                    ['id' => 70],
                ]
            );
        $expectedCommunityLicData = [
            [
                'id' => 69,
                'status' => CommunityLic::STATUS_VOID,
                'expiredDate' => $date,
            ],
            [
                'id' => 70,
                'status' => CommunityLic::STATUS_VOID,
                'expiredDate' => $date,
            ],
        ];
        $mockCommunityLicEntityService->shouldReceive('multiUpdate')
            ->once()
            ->with($expectedCommunityLicData);

        $mockLicenceEntityService->shouldReceive('updateCommunityLicencesCount')
            ->once()
            ->with($licenceId);

        $this->sut->processNotTakenUpApplication($applicationId);
    }
}
