<?php

/**
 * Licence Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Adapters;

use CommonTest\Bootstrap;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\Controller\Lva\Adapters\LicenceVehiclesPsvAdapter;

/**
 * Licence Vehicles Psv Adapter Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehiclesPsvAdapterTest extends MockeryTestCase
{
    protected $sut;
    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();

        $this->sut = new LicenceVehiclesPsvAdapter();
        $this->sut->setServiceLocator($this->sm);
    }

    public function testGetVehiclesData()
    {
        $mockLicenceEntity = m::mock();
        $this->sm->setService('Entity\Licence', $mockLicenceEntity);

        $mockLicenceEntity->shouldReceive('getVehiclesPsvData')
            ->with(3)
            ->andReturn('RESPONSE');

        $this->assertEquals('RESPONSE', $this->sut->getVehiclesData(3));
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
