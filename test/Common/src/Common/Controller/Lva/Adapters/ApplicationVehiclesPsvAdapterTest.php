<?php

/**
 * Application Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\ApplicationVehiclesPsvAdapter;

/**
 * Application Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
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
}
