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

    public function testGetVehiclesData()
    {
        $mockApplicationVehiclesGoodsAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockApplicationVehiclesGoodsAdapter);

        $mockApplicationVehiclesGoodsAdapter->shouldReceive('getVehiclesData')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
    }

    public function testSave()
    {
        $this->assertNull($this->sut->save([], 1));
    }

    public function testGetFormData()
    {
        $id = 3;

        $this->assertEquals([], $this->sut->getFormData($id));
    }

    public function testShowFilters()
    {
        $this->assertTrue($this->sut->showFilters());
    }
}
