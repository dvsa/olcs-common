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
use PHPUnit_Framework_TestCase;

/**
 * Application Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationProcessingServiceTest extends PHPUnit_Framework_TestCase
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
        $this->mockLicenceVehicles($licenceId, $mockLicenceVehicles);

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
                    'action' => 'A',
                    'operatingCentre' => 4,
                    'licence' => $licenceId
                )
            );
        $mockLicenceOperatingCentre->expects($this->at(1))
            ->method('save')
            ->with(
                array(
                    'action' => 'A',
                    'operatingCentre' => 5,
                    'licence' => $licenceId
                )
            );
        $this->sm->setService('Entity\LicenceOperatingCentre', $mockLicenceOperatingCentre);
    }

    protected function mockLicenceVehicles($licenceId, $mockLicenceVehicles)
    {
        $mockLicenceVehicle = $this->getMock('\stdClass', ['getForApplicationValidation']);
        $mockLicenceVehicle->expects($this->once())
            ->method('getForApplicationValidation')
            ->with($licenceId)
            ->will($this->returnValue($mockLicenceVehicles));
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);
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
