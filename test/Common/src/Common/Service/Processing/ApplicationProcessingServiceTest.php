<?php

/**
 * Application Processing Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Service\Processing;

use Common\RefData;
use CommonTest\Bootstrap;
use CommonTest\Traits\MockDateTrait;
use Common\Service\Processing\ApplicationProcessingService;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Service\Data\FeeTypeDataService;
use Mockery as m;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;

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

    protected function mockApplicationService($id, $licenceId, $validationData)
    {
        $mockApplicationService = $this->createMock(
            '\stdClass',
            ['getLicenceIdForApplication', 'forceUpdate', 'getDataForValidating', 'getCategory']
        );
        $mockApplicationService->expects($this->once())
            ->method('getLicenceIdForApplication')
            ->with($id)
            ->will($this->returnValue($licenceId));

        $appStatusData = array('status' => RefData::APPLICATION_STATUS_VALID);

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
        $mockLicenceService = $this->createMock('\stdClass', ['forceUpdate']);
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

        $mockApplicationOperatingCentre = $this->createMock('\stdClass', ['getForApplication', 'clearInterims']);
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
        $mockLicenceOperatingCentre = $this->createMock('\stdClass', ['save']);
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
        $mockLicenceVehicle = $this->createMock('\stdClass', ['getForApplicationValidation', 'multiUpdate']);
        $mockLicenceVehicle->expects($this->once())
            ->method('getForApplicationValidation')
            ->with($licenceId)
            ->will($this->returnValue($mockLicenceVehicles));
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        return $mockLicenceVehicle;
    }

    protected function expectSuccessMessage()
    {
        $mockFlashMessenger = $this->createMock('\stdClass', ['addSuccessMessage']);
        $mockFlashMessenger->expects($this->once())
            ->method('addSuccessMessage')
            ->with('licence-valid-confirmation');
        $this->sm->setService('Helper\FlashMessenger', $mockFlashMessenger);
    }

    public function enforcementAreaIsValidProvider()
    {
        return [
            [
                ['enforcementArea' => 'FOO'],
                true,
            ],
            [
                ['enforcementArea' => ''],
                false,
            ],
            [
                ['enforcementArea' => null],
                false,
            ],
        ];
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
                    [RefData::FEE_STATUS_OUTSTANDING],
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
            [RefData::APPLICATION_TYPE_VARIATION, FeeTypeDataService::FEE_TYPE_VAR],
            [RefData::APPLICATION_TYPE_NEW, FeeTypeDataService::FEE_TYPE_APP],
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
                    [RefData::FEE_STATUS_OUTSTANDING],
                    $applicationId
                )
                ->andReturn($fee)
                ->getMock()
        );

        $this->assertEquals($fee, $this->sut->getInterimFee($applicationId));
    }

    public function testExpireCommunityLicencesForLicence()
    {
        $mockLicenceEntityService = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntityService);

        $mockCommunityLicEntityService = m::mock();
        $this->sm->setService('Entity\CommunityLic', $mockCommunityLicEntityService);

        $mockDateHelper = m::mock();
        $this->sm->setService('Helper\Date', $mockDateHelper);

        $communityLicences = [
            ['id' => 1, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_PENDING]],
            ['id' => 2, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_EXPIRED]],
            ['id' => 3, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_VOID]],
            ['id' => 4, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_ACTIVE]],
            ['id' => 5, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_WITHDRAWN]],
            ['id' => 6, 'status' => ['id' => RefData::COMMUNITY_LICENCE_STATUS_SUSPENDED]],
        ];

        $mockLicenceEntityService->shouldReceive('getCommunityLicencesByLicenceId')->with(1966)->once()
            ->andReturn($communityLicences);

        $mockDateHelper->shouldReceive('getDate')->with(\DateTime::W3C)->once()->andReturn('DATETIME');

        $mockCommunityLicEntityService->shouldReceive('multiUpdate')
            ->with(
                [
                    ['id' => 1, 'status' => RefData::COMMUNITY_LICENCE_STATUS_EXPIRED, 'expiredDate' => 'DATETIME'],
                    ['id' => 4, 'status' => RefData::COMMUNITY_LICENCE_STATUS_EXPIRED, 'expiredDate' => 'DATETIME'],
                    ['id' => 6, 'status' => RefData::COMMUNITY_LICENCE_STATUS_EXPIRED, 'expiredDate' => 'DATETIME'],
                ]
            )
            ->once();

        $mockLicenceEntityService->shouldReceive('updateCommunityLicencesCount')->with(1966)->once();

        $this->sut->expireCommunityLicencesForLicence(1966);
    }
}
