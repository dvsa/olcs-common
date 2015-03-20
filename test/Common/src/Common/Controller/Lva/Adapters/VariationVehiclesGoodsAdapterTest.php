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

    public function testGetFilterForm()
    {
        $mockApplicationVehiclesGoodsAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockApplicationVehiclesGoodsAdapter);

        $mockApplicationVehiclesGoodsAdapter->shouldReceive('getFilterForm')
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getFilterForm());
    }

    public function testGetFilters()
    {
        $mockApplicationVehiclesGoodsAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockApplicationVehiclesGoodsAdapter);

        $mockApplicationVehiclesGoodsAdapter->shouldReceive('getFilters')
            ->with(['foo' => 'bar'])
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getFilters(['foo' => 'bar']));
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
}
