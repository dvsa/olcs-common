<?php

/**
 * Variation Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\VariationVehiclesGoodsAdapter;

/**
 * Variation Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new VariationVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetFormData()
    {
        $id = 3;

        $this->assertEquals([], $this->sut->getFormData($id));
    }

    public function testGetFilteredVehiclesData()
    {
        $id = 111;
        $query = [
            'foo' => 'bar'
        ];
        $filters = [
            'bar' => 'foo'
        ];

        $mockAppAdapter = m::mock();
        $mockLicenceVehicle = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockAppAdapter);
        $this->sm->setService('Entity\LicenceVehicle', $mockLicenceVehicle);

        $mockAppAdapter->shouldReceive('formatFilters')
            ->with($query)
            ->andReturn($filters);

        $mockLicenceVehicle->shouldReceive('getVehiclesDataForVariation')
            ->with(111, $filters)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getFilteredVehiclesData($id, $query));
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
