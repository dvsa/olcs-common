<?php

/**
 * Application Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\ApplicationGoodsVehiclesVehicle;

/**
 * Application Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new ApplicationGoodsVehiclesVehicle();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->formService);
    }

    public function testGetForm()
    {
        // Params
        $mockRequest = m::mock();
        $params = [
            'isRemoved' => false
        ];

        // Mocks
        $mockForm = m::mock();
        $mockGoodsVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');
        $mockGenericVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');

        $this->formService->setService('lva-goods-vehicles-vehicle', $mockGoodsVehiclesVehicle);
        $this->formService->setService('lva-generic-vehicles-vehicle', $mockGenericVehiclesVehicle);

        // Expectations
        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\GoodsVehiclesVehicle', $mockRequest)
            ->andReturn($mockForm);

        $mockGoodsVehiclesVehicle->shouldReceive('alterForm')
            ->with($mockForm, $params);

        $mockGenericVehiclesVehicle->shouldReceive('alterForm')
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

    public function testGetFormIsRemoved()
    {
        // Params
        $mockRequest = m::mock();
        $params = [
            'isRemoved' => true
        ];

        // Mocks
        $mockForm = m::mock();
        $mockGoodsVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');
        $mockGenericVehiclesVehicle = m::mock('\Common\FormService\FormServiceInterface');

        $this->formService->setService('lva-goods-vehicles-vehicle', $mockGoodsVehiclesVehicle);
        $this->formService->setService('lva-generic-vehicles-vehicle', $mockGenericVehiclesVehicle);

        // Expectations
        $this->formHelper->shouldReceive('createFormWithRequest')
            ->with('Lva\GoodsVehiclesVehicle', $mockRequest)
            ->andReturn($mockForm);

        $mockGoodsVehiclesVehicle->shouldReceive('alterForm')
            ->with($mockForm, $params);

        $mockGenericVehiclesVehicle->shouldReceive('alterForm')
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

        $this->formHelper->shouldReceive('disableElements')
            ->with($mockForm)
            ->shouldReceive('remove')
            ->with($mockForm, 'form-actions->submit');

        $mockForm->shouldReceive('get')
            ->with('form-actions')
            ->andReturn(
                m::mock()
                ->shouldReceive('get')
                ->with('cancel')
                ->andReturn(
                    m::mock()
                    ->shouldReceive('setAttribute')
                    ->with('disabled', false)
                    ->getMock()
                )
                ->getMock()
            );

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }
}
