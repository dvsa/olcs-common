<?php

namespace CommonTest\Common\FormService\Form\Lva\ConditionsUndertakings;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\ConditionsUndertakings\VariationConditionsUndertakings as Sut;
use Laminas\Form\Form;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Conditions Undertakings Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakingsTest extends MockeryTestCase
{
    /**
     * @var \Mockery\LegacyMockInterface
     */
    public $authService;
    protected $sut;

    protected $formHelper;

    protected $fsm;

    protected function setUp(): void
    {
        $this->formHelper = m::mock(\Common\Service\Helper\FormHelperService::class);
        $this->fsm = m::mock(\Common\FormService\FormServiceManager::class)->makePartial();
        $this->authService = m::mock(AuthorizationService::class);

        $this->sut = new Sut($this->formHelper, $this->authService);
    }

    public function testGetForm(): void
    {
        $formActions = m::mock(\Laminas\Form\ElementInterface::class);
        $formActions->shouldReceive('has')->with('save')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('save');
        $formActions->shouldReceive('has')->with('cancel')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('cancel');
        $formActions->shouldReceive('has')->with('saveAndContinue')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('saveAndContinue');

        $form = m::mock(\Common\Form\Form::class);
        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\ConditionsUndertakings')
            ->andReturn($form);

        $this->sut->getForm();
    }
}
