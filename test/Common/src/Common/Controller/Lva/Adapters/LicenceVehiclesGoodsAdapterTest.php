<?php

/**
 * Licence Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceVehiclesGoodsAdapter;

/**
 * Licence Vehicles Goods Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesGoodsAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceVehiclesGoodsAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetVehiclesData()
    {
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockLicenceEntity->shouldReceive('getVehiclesData')
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
        $form = m::mock();

        $mockApplicationVehiclesGoodsAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockApplicationVehiclesGoodsAdapter);

        $mockApplicationVehiclesGoodsAdapter->shouldReceive('getFilterForm')
            ->andReturn($form);

        $this->sm->setService(
            'Helper\Form',
            m::mock()
            ->shouldReceive('remove')
            ->with($form, 'specified')
            ->getMock()
        );

        $this->assertEquals($form, $this->sut->getFilterForm());
    }

    public function testGetFilters()
    {
        $form = m::mock();

        $mockApplicationVehiclesGoodsAdapter = m::mock();
        $this->sm->setService('ApplicationVehiclesGoodsAdapter', $mockApplicationVehiclesGoodsAdapter);

        $mockApplicationVehiclesGoodsAdapter->shouldReceive('getFilters')
            ->with(['foo' => 'bar'])
            ->andReturn(['foo' => 'bar']);

        $expected = [
            'foo' => 'bar',
            'specified' => 'Y'
        ];

        $this->assertEquals($expected, $this->sut->getFilters(['foo' => 'bar']));
    }
}
