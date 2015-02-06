<?php

/**
 * Vehicle Form Adapter Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace CommonTest\Service\VehicleFormAdapter;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use CommonTest\Bootstrap;
use Common\Service\VehicleFormAdapter\VehicleFormAdapterService;

/**
 * Vehicle Form Adapter Service Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehicleFormAdapterServiceTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->sut = new VehicleFormAdapterService();
        $this->sut->setServiceLocator($this->sm);
    }

    /**
     * Test alter form
     *
     * @group vehcileFormAdapter
     */
    public function testSetAlterForm()
    {
        $mockForm = m::mock('Zend\Form\Form')
            ->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('setAttribute')
                ->with('class', 'visually-hidden')
                ->getMock()
            )
            ->getMock();

        $mockFormHelper = m::mock()
            ->shouldReceive('remove')
            ->with($mockForm, 'licence-vehicle->specifiedDate')
            ->shouldReceive('remove')
            ->with($mockForm, 'licence-vehicle->removalDate')
            ->shouldReceive('remove')
            ->with($mockForm, 'licence-vehicle->discNo')
            ->getMock();

        $this->sm->setService('Helper\Form', $mockFormHelper);

        $this->assertInstanceOf('Zend\Form\Form', $this->sut->alterForm($mockForm));
    }
}
