<?php

/**
 * Application Psv Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\ApplicationPsvVehiclesVehicle;

/**
 * Application Psv Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationPsvVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new ApplicationPsvVehiclesVehicle();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->formService);
    }

    public function testGetForm()
    {
        $mockRequest = m::mock();
        $params = [
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

        $mockLicenceVehicle = m::mock();
        $mockSpecifiedDate = m::mock();
        $mockRemovalDate = m::mock();

        $mockForm->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn($mockLicenceVehicle);

        $mockLicenceVehicle->shouldReceive('get')
            ->with('specifiedDate')
            ->andReturn($mockSpecifiedDate)
            ->shouldReceive('get')
            ->with('removalDate')
            ->andReturn($mockRemovalDate);

        $this->formHelper->shouldReceive('disableDateElement')
            ->with($mockSpecifiedDate)
            ->shouldReceive('disableDateElement')
            ->with($mockRemovalDate);

        // <<-- END SUT::alterForm

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }

    public function testGetFormWithoutSmall()
    {
        $mockRequest = m::mock();
        $params = [
            'action' => 'medium-add'
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

        $mockLicenceVehicle = m::mock();
        $mockSpecifiedDate = m::mock();
        $mockRemovalDate = m::mock();

        $mockForm->shouldReceive('get')
            ->with('licence-vehicle')
            ->andReturn($mockLicenceVehicle);

        $mockLicenceVehicle->shouldReceive('get')
            ->with('specifiedDate')
            ->andReturn($mockSpecifiedDate)
            ->shouldReceive('get')
            ->with('removalDate')
            ->andReturn($mockRemovalDate);

        $this->formHelper->shouldReceive('disableDateElement')
            ->with($mockSpecifiedDate)
            ->shouldReceive('disableDateElement')
            ->with($mockRemovalDate);

        // <<-- END SUT::alterForm

        $this->formHelper->shouldReceive('remove')
            ->with($mockForm, 'data->isNovelty')
            ->shouldReceive('remove')
            ->with($mockForm, 'data->makeModel');

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }
}
