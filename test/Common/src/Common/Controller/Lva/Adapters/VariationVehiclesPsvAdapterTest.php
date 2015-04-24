<?php

/**
 * Variation Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationVehiclesPsvAdapter;

/**
 * Variation Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesPsvAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationVehiclesPsvAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetVehiclesData()
    {
        $mockApplicationVehiclesPsvAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesPsvAdapter', $mockApplicationVehiclesPsvAdapter);

        $mockApplicationVehiclesPsvAdapter->shouldReceive('getVehiclesData')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
    }

    public function testWarnIfAuthorityExceeded()
    {
        $id = 69;
        $types = ['foo','bar'];

        $this->sm->setService(
            'ApplicationVehiclesPsvAdapter',
            m::mock()
                ->shouldReceive('warnIfAuthorityExceeded')
                ->with($id, $types, true)
                ->once()
                ->getMock()
        );

        $this->sut->warnIfAuthorityExceeded($id, $types, true);
    }

    public function testAlterVehcileTable()
    {
        $mockTable = m::mock('Common\Service\Table\TableBuilder')
            ->shouldReceive('removeAction')
            ->with('transfer')
            ->once()
            ->andReturnSelf()
            ->getMock();

        $this->assertInstanceOf('Common\Service\Table\TableBuilder', $this->sut->alterVehcileTable($mockTable, null));
    }
}
