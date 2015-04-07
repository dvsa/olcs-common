<?php

/**
 * Variation Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\VariationGoodsVehicles;
use CommonTest\Bootstrap;

/**
 * Variation Goods Vehicles Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehiclesTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $formService;

    protected $sm;

    public function setUp()
    {
        $this->sm = Bootstrap::getServiceManager();
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->formService = m::mock('\Common\FormService\FormServiceManager')->makePartial();

        $this->sut = new VariationGoodsVehicles();
        $this->sut->setFormHelper($this->formHelper);
        $this->sut->setServiceLocator($this->sm);
        $this->sut->setFormServiceLocator($this->formService);
    }

    public function testGetForm()
    {
        // Params
        $mockTable = m::mock('\Common\Service\Table\TableBuilder');
        $isCrudPressed = true;

        // Mocks
        $mockForm = m::mock();
        $mockTableElement = m::mock('\Zend\Form\Fieldset');
        $mockValidator = m::mock();

        $this->sm->setService('oneRowInTablesRequired', $mockValidator);

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
        $mockVariation = m::mock('\Common\FormService\FormServiceInterface');
        $this->formService->setService('lva-variation', $mockVariation);

        $mockLicenceVariationVehicles = m::mock('\Common\FormService\FormServiceInterface');
        $this->formService->setService('lva-licence-variation-vehicles', $mockLicenceVariationVehicles);

        $mockVariation->shouldReceive('alterForm')
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
