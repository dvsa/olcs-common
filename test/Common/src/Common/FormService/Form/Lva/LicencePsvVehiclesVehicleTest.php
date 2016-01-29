<?php

/**
 * Licence Psv Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicencePsvVehiclesVehicle;

/**
 * Licence Psv Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePsvVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    public function setUp()
    {
        $this->markTestSkipped();
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new LicencePsvVehiclesVehicle();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->formService);
    }

    public function testGetFormAdd()
    {
        $mockRequest = m::mock();
        $params = [
            'mode' => 'add',
            'action' => 'small-add'
        ];

        // Mocks
        $mockForm = m::mock();
        $mockPsvVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');
        $mockGenericVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');

        $this->formService->setService('lva-psv-vehicles-vehicle', $mockPsvVehiclesVehicle);
        $this->formService->setService('lva-generic-vehicles-vehicle', $mockGenericVehiclesVehicle);

        // Expectations
        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\PsvVehiclesVehicle', $mockRequest)
            ->andReturn($mockForm)
            ->shouldReceive('remove')
            ->with($mockForm, 'licence-vehicle->discNo');

        $mockPsvVehiclesVehicle->shouldReceive('alterForm')
            ->once()
            ->with($mockForm);

        $mockGenericVehiclesVehicle->shouldReceive('alterForm')
            ->once()
            ->with($mockForm, $params);

        // <<-- START SUT::alterForm

        // <<-- END SUT::alterForm

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }

    public function testGetFormEdit()
    {
        $mockRequest = m::mock();
        $params = [
            'mode' => 'edit',
            'action' => 'small-add'
        ];

        // Mocks
        $mockForm = m::mock();
        $mockPsvVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');
        $mockGenericVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');

        $this->formService->setService('lva-psv-vehicles-vehicle', $mockPsvVehiclesVehicle);
        $this->formService->setService('lva-generic-vehicles-vehicle', $mockGenericVehiclesVehicle);

        // Expectations
        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\PsvVehiclesVehicle', $mockRequest)
            ->andReturn($mockForm)
            ->shouldReceive('remove')
            ->with($mockForm, 'licence-vehicle->discNo');

        $mockPsvVehiclesVehicle->shouldReceive('alterForm')
            ->once()
            ->with($mockForm);

        $mockGenericVehiclesVehicle->shouldReceive('alterForm')
            ->once()
            ->with($mockForm, $params);

        // <<-- START SUT::alterForm

        $mockForm->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('specifiedDate')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setShouldCreateEmptyOption')
                    ->with(false)
                    ->getMock()
                )
                ->getMock()
            );

        // <<-- END SUT::alterForm

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }
}
