<?php

/**
 * Application Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationVehiclesPsvAdapter;
use Common\Service\Entity\VehicleEntityService;

/**
 * Application Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ApplicationVehiclesPsvAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new ApplicationVehiclesPsvAdapter();
        $this->sut->setServiceLocator($this->sm);

        // stub the mapping between type and psv type that is now in entity service
        $map = [
            'small'  => 'vhl_t_a',
            'medium' => 'vhl_t_b',
            'large'  => 'vhl_t_c',
        ];
        $this->sm->setService(
            'Entity\Vehicle',
            m::mock()
                ->shouldReceive('getTypeFromPsvType')
                ->andReturnUsing(
                    function ($type) use ($map) {
                        return array_search($type, $map);
                    }
                )
                ->getMock()
        );
    }

    public function testGetVehiclesData()
    {
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockLicenceEntity->shouldReceive('getVehiclesPsvDataForApplication')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
    }

    /**
     * Test maybeDisableRemovedAndSpecifiedDates method
     */
    public function testMaybeDisableRemovedAndSpecifiedDates()
    {
        $mockForm = m::mock()
            ->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('specifiedDate')
                ->andReturn('specifiedDate')
                ->once()
                ->shouldReceive('get')
                ->with('removalDate')
                ->andReturn('removedDate')
                ->once()
                ->getMock()
            )
            ->once()
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('disableDateElement')
            ->with('specifiedDate')
            ->once()
            ->shouldReceive('disableDateElement')
            ->with('removedDate')
            ->once()
            ->getMock();

        $this->assertEquals(null, $this->sut->maybeDisableRemovedAndSpecifiedDates($mockForm, $mockFormHelper));
    }

    /**
     * Test maybeFormatRemovedAndSpecifiedDates method
     */
    public function testMaybeFormatRemovedAndSpecifiedDates()
    {
        $this->assertEquals('data', $this->sut->maybeFormatRemovedAndSpecifiedDates('data'));
    }

    /**
     * Test maybeUnsetSpecifiedDate method
     */
    public function testMaybeUnsetSpecifiedDate()
    {
        $this->assertEquals(
            ['licence-vehicle' => []],
            $this->sut->maybeUnsetSpecifiedDate(['licence-vehicle' => ['specifiedDate' => 'date']])
        );
    }

    /**
     * Test that flash warning message is added when we render the page and
     * an authority for individual vehicle type is exceeded. (This can only
     * happen when vehicles were previously added but then the Operating Centre
     * authority is decreased)
     */
    public function testWarnIfAuthorityExceeded()
    {
        $id = 69;

        $entityData = [
            'id'                    => $id,
            'hasEnteredReg'         => 'Y',
            'totAuthVehicles'       => 5,
            'totAuthSmallVehicles'  => 2,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles'  => 0,
        ];

        $vehiclesData = [
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_SMALL]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_SMALL]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_MEDIUM]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_MEDIUM]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_LARGE]]],
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                    ->with($id)
                    ->once()
                    ->andReturn($entityData)
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('getVehiclesPsvDataForApplication')
                    ->with($id)
                    ->once()
                    ->andReturn($vehiclesData)
                ->getMock()
        );

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
                ->shouldReceive('addCurrentWarningMessage')
                    ->once()
                    ->with('more-vehicles-than-large-authorisation')
                ->shouldReceive('addWarningMessage')
                    ->never()
                ->getMock()
        );

        $psvTypes = [
            VehicleEntityService::PSV_TYPE_SMALL,
            VehicleEntityService::PSV_TYPE_MEDIUM,
            VehicleEntityService::PSV_TYPE_LARGE,
        ];
        $this->sut->warnIfAuthorityExceeded($id, $psvTypes, false);

    }

    /**
     * Test that flash warning messages are not added if user selects they are
     * not submitting vehicle details
     */
    public function testWarnIfAuthorityExceededNotSubmitting()
    {
        $id = 69;

        $entityData = [
            'id'                    => $id,
            'hasEnteredReg'         => 'N',
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                    ->with($id)
                    ->once()
                    ->andReturn($entityData)
                ->getMock()
        );

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
                ->shouldReceive('addWarningMessage')->never()
                ->shouldReceive('addCurrentWarningMessage')->never()
                ->getMock()
        );
        $psvTypes = [
            VehicleEntityService::PSV_TYPE_SMALL,
            VehicleEntityService::PSV_TYPE_MEDIUM,
            VehicleEntityService::PSV_TYPE_LARGE,
        ];
        $this->sut->warnIfAuthorityExceeded($id, $psvTypes, true);
        $this->sut->warnIfAuthorityExceeded($id, $psvTypes, false);
    }

    /**
     * Test that correct flash warning message is added on save when an
     * authority is exceeded
     */
    public function testAddWarningsIfAuthorityExceededRedirect()
    {
        $id = 69;

        $entityData = [
            'id'                    => $id,
            'hasEnteredReg'         => 'Y',
            'totAuthVehicles'       => 5,
            'totAuthSmallVehicles'  => 1,
            'totAuthMediumVehicles' => 3,
            'totAuthLargeVehicles'  => 1,
        ];

        $vehiclesData = [
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_SMALL]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_SMALL]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_LARGE]]],
            ['vehicle' => ['psvType' => ['id' => VehicleEntityService::PSV_TYPE_LARGE]]],
        ];

        $this->sm->setService(
            'Entity\Application',
            m::mock()
                ->shouldReceive('getDataForVehiclesPsv')
                    ->with($id)
                    ->once()
                    ->andReturn($entityData)
                ->getMock()
        );

        $this->sm->setService(
            'Entity\Licence',
            m::mock()
                ->shouldReceive('getVehiclesPsvDataForApplication')
                    ->with($id)
                    ->once()
                    ->andReturn($vehiclesData)
                ->getMock()
        );

        $this->sm->setService(
            'Helper\FlashMessenger',
            m::mock()
                ->shouldReceive('addWarningMessage')
                    ->once()
                    ->with('more-vehicles-than-large-authorisation')
                ->shouldReceive('addWarningMessage')
                    ->once()
                    ->with('more-vehicles-than-small-authorisation')
                ->shouldReceive('addCurrentWarningMessage')->never()
                ->getMock()
        );

        $psvTypes = [
            VehicleEntityService::PSV_TYPE_SMALL,
            VehicleEntityService::PSV_TYPE_MEDIUM,
            VehicleEntityService::PSV_TYPE_LARGE,
        ];
        $this->sut->warnIfAuthorityExceeded($id, $psvTypes, true);
    }
}
