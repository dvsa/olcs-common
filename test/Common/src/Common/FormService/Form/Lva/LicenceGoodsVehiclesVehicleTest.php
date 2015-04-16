<?php

/**
 * Licence Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicenceGoodsVehiclesVehicle;

/**
 * Licence Goods Vehicles Vehicle Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesVehicleTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    public function setUp()
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new LicenceGoodsVehiclesVehicle();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setFormServiceLocator($this->formService);
    }

    public function testGetForm()
    {
        // Params
        $mockRequest = m::mock();
        $params = [
            'mode' => 'add',
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

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }

    public function testGetFormIsRemoved()
    {
        // Params
        $mockRequest = m::mock();
        $params = [
            'mode' => 'add',
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

    public function testGetFormWithEdit()
    {
        // Params
        $mockRequest = m::mock();
        $params = [
            'mode' => 'edit',
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

        $mockForm->shouldReceive('get->get->setShouldCreateEmptyOption')
            ->with(false);

        $form = $this->sut->getForm($mockRequest, $params);

        $this->assertSame($mockForm, $form);
    }
}
