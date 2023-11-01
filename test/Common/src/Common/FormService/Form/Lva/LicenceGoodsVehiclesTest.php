<?php

namespace CommonTest\Common\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\LicenceGoodsVehicles;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    protected $sm;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formHelper->shouldReceive('getServiceLocator')
            ->andReturn($this->sm);
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $this->authService = m::mock(AuthorizationService::class);

        $this->sut = new LicenceGoodsVehicles($this->formHelper, $this->authService, $this->formService);
    }

    public function testGetForm()
    {
        // Params
        $mockTable = m::mock('\Common\Service\Table\TableBuilder');
        $isCrudPressed = true;

        // Mocks
        $mockForm = m::mock();
        $mockTableElement = m::mock('\Laminas\Form\Fieldset');
        $mockValidator = m::mock();

        // Expectations
        $this->formHelper->shouldReceive('createForm')
            ->with('Lva\GoodsVehicles')
            ->andReturn($mockForm)
            ->shouldReceive('populateFormTable')
            ->with($mockTableElement, $mockTable);

        $mockForm->shouldReceive('get')
            ->with('table')
            ->andReturn($mockTableElement);

        $mockForm->shouldReceive('getInputFilter->get->get->getValidatorChain->attach')
            ->with($mockValidator);

        // <<--- START SUT::alterForm
        $mockLicence = m::mock('\Common\FormService\FormServiceInterface');
        $this->formService->setService('lva-licence', $mockLicence);

        $mockLicenceVariationVehicles = m::mock('\Common\FormService\FormServiceInterface');
        $this->formService->setService('lva-licence-variation-vehicles', $mockLicenceVariationVehicles);

        $mockLicence->shouldReceive('alterForm')
            ->with($mockForm);

        $mockLicenceVariationVehicles->shouldReceive('alterForm')
            ->with($mockForm);
        // <<--- END SUT::alterForm

        $mockTableElement->shouldReceive('get->getValue')
            ->andReturn(10);

        $mockValidator->shouldReceive('setRows')
            ->with([10])
            ->shouldReceive('setCrud')
            ->with(true);

        $form = $this->sut->getForm($mockTable, $isCrudPressed);

        $this->assertSame($mockForm, $form);
    }
}
