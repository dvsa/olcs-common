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

    public function testAlterVehcileTable()
    {
        $mockTable = m::mock('Common\Service\Table\TableBuilder')
            ->shouldReceive('removeAction')
            ->with('transfer')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $this->assertInstanceOf('Common\Service\Table\TableBuilder', $this->sut->alterVehicleTable($mockTable, null));
    }
}
