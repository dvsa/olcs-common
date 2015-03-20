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

    /**
     * Disable maybeFormatRemovedAndSpecifiedDates method
     */
    public function testMaybeFormatRemovedAndSpecifiedDates()
    {
        $licenceVehicle = [
            'removalDate' => [
                'month' => 1,
                'day' => 2,
                'year' => 2014
            ],
            'specifiedDate' => [
                'month' => 3,
                'day' => 4,
                'year' => 2015
            ]
        ];
        $this->assertEquals(
            ['removalDate' => '2014-1-2', 'specifiedDate' => '2015-3-4'],
            $this->sut->maybeFormatRemovedAndSpecifiedDates($licenceVehicle)
        );
    }

    /**
     * Disable maybeFormatRemovedAndSpecifiedDates method with wrong dates
     */
    public function testMaybeFormatRemovedAndSpecifiedDatesWrong()
    {
        $licenceVehicle = [
            'removalDate' => [
                'month' => 2,
                'day' => 30,
                'year' => 2014
            ],
            'specifiedDate' => [
                'month' => 2,
                'day' => 30,
                'year' => 2015
            ]
        ];
        $this->assertEquals([], $this->sut->maybeFormatRemovedAndSpecifiedDates($licenceVehicle));
    }

    /**
     * Disable maybeDisableRemovedAndSpecifiedDates method
     */
    public function testMaybeDisableRemovedAndSpecifiedDates()
    {
        $this->assertEquals(null, $this->sut->maybeDisableRemovedAndSpecifiedDates('form', 'formHelper'));
    }

    /**
     * Disable maybeUnsetSpecifiedDate method
     */
    public function testMaybeUnsetSpecifiedDate()
    {
        $this->assertEquals('data', $this->sut->maybeUnsetSpecifiedDate('data'));
    }
}
