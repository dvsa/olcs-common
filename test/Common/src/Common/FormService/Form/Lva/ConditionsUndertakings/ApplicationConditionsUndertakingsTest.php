<?php

namespace CommonTest\FormService\Form\Lva\ConditionsUndertakings;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\ConditionsUndertakings\ApplicationConditionsUndertakings as Sut;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

/**
 * Application Conditions Undertakings Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationConditionsUndertakingsTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $fsm;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
        $this->fsm = m::mock('\Common\FormService\FormServiceManager')->makePartial();
        $this->authService = m::mock(AuthorizationService::class);

        $this->sut = new Sut($this->formHelper, $this->authService);
    }

    public function testGetForm()
    {
        $formActions = m::mock();
        $formActions->shouldReceive('has')->with('save')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('save');
        $formActions->shouldReceive('has')->with('cancel')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('cancel');

        $form = m::mock();
        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\ConditionsUndertakings')
            ->andReturn($form);

        $this->sut->getForm();
    }
}
