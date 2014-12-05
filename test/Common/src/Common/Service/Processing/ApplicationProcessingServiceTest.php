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
            'taskSubCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
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
                )
            ),
            array(
                'action' => 'D',
                'operatingCentre' => array(
                    'id' => 6
                )
            )
        );

        // For PSV we only care about the count
        $stubbedLicenceVehicles = array(
            array(
                'foo' => 'bar'
            ),
            array(
                'foo' => 'bar'
            )
        );

        $expectedPsvDiscData = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
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
            );

        // createDiscRecords
        $mockLicenceVehicleService = m::mock();
        $mockLicenceVehicleService->shouldReceive('getForApplicationValidation')
            ->with($licenceId)
            ->andReturn($stubbedLicenceVehicles)
            ->shouldReceive('multiUpdate')
            ->with(
                array(
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01'
                    ),
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01'
                    )
                )
            );

        // createPsvDiscs
        $mockPsvDiscService = m::mock();
        $mockPsvDiscService->shouldReceive('requestDiscs')
            ->with(2, $expectedPsvDiscData);

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

        // For PSV we only care about the count
        $stubbedLicenceVehicles = array(
            array(
                'foo' => 'bar'
            ),
            array(
                'foo' => 'bar'
            )
        );

        $expectedPsvDiscData = array(
            'licence' => $licenceId,
            'ceasedDate' => null,
            'issuedDate' => null,
            'discNo' => null,
            'isCopy' => 'N'
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
        $mockLicenceVehicleService = m::mock();
        $mockLicenceVehicleService->shouldReceive('getForApplicationValidation')
            ->with($licenceId)
            ->andReturn($stubbedLicenceVehicles)
            ->shouldReceive('multiUpdate')
            ->with(
                array(
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01'
                    ),
                    array(
                        'foo' => 'bar',
                        'specifiedDate' => '2012-01-01'
                    )
                )
            );

        // createPsvDiscs
        $mockPsvDiscService = m::mock();
        $mockPsvDiscService->shouldReceive('requestDiscs')
            ->with(2, $expectedPsvDiscData);

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
            'taskSubCategory' => CategoryDataService::TASK_SUB_CATEGORY_APPLICATION_GRANT_FEE_DUE,
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

        $mockApplicationService = $this->mockApplicationService($id, $licenceId, $validationData);

        $this->mockLicenceService($licenceId, $expectedLicenceData);

        $this->mockApplicationOperatingCentre($id);

        $this->mockLicenceOperatingCentre($licenceId);

        $mockLicenceVehicles = array(
            array(
                'id' => 1
            ),
            array(
                'id' => 2
            )
        );
        $mockLicenceVehicleService = $this->mockLicenceVehicles($licenceId, $mockLicenceVehicles);

        $mockLicenceVehicleService->expects($this->once())
            ->method('multiUpdate')
            ->with(
                array(
                    array(
                        'id' => 1,
                        'specifiedDate' => $date
                    ),
                    array(
                        'id' => 2,
                        'specifiedDate' => $date
                    )
                )
            );

        $mockApplicationService->expects($this->once())
            ->method('getCategory')
            ->with($id)
            ->will($this->returnValue(LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE));

        $goodsDiscMock = $this->getMock('\stdClass', ['save']);
        $goodsDiscMock->expects($this->at(0))
            ->method('save')
            ->with(
                array(
                    'ceasedDate' => null,
                    'issuedDate' => null,
                    'discNo' => null,
                    'isCopy' => 'N',
                    'licenceVehicle' => 1
                )
            );
        $goodsDiscMock->expects($this->at(1))
            ->method('save')
            ->with(
                array(
                    'ceasedDate' => null,
                    'issuedDate' => null,
                    'discNo' => null,
                    'isCopy' => 'N',
                    'licenceVehicle' => 2
                )
            );
        $this->sm->setService('Entity\GoodsDisc', $goodsDiscMock);

        $this->expectSuccessMessage();

        $this->sut->validateApplication($id);
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
                'operatingCentre' => array(
                    'id' => 4
                )
            ),
            array(
                'action' => 'A',
                'operatingCentre' => array(
                    'id' => 5
                )
            )
        );

        $mockApplicationOperatingCentre = $this->getMock('\stdClass', ['getForApplication']);
        $mockApplicationOperatingCentre->expects($this->once())
            ->method('getForApplication')
            ->with($id)
            ->will($this->returnValue($aocData));
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
}
