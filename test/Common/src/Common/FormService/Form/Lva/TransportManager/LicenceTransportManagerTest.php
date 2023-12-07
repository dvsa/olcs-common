<?php

namespace CommonTest\Common\FormService\Form\Lva\TransportManager;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Common\FormService\Form\Lva\TransportManager\LicenceTransportManager as Sut;
use Laminas\Form\Form;
use ZfcRbac\Service\AuthorizationService;

/**
 * Licence TransportManager Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTransportManagerTest extends MockeryTestCase
{
    protected $sut;

    protected $formHelper;

    protected $fsm;

    public function setUp(): void
    {
        $this->formHelper = m::mock('\Common\Service\Helper\FormHelperService');
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

        $formActions->shouldReceive('has')->with('saveAndContinue')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('saveAndContinue');

        $form = m::mock();
        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\TransportManagers')
            ->andReturn($form);

        $this->sut->getForm();
    }

    public function testGetFormWithoutFormActions()
    {
        $form = m::mock();
        $form->shouldReceive('has')->with('form-actions')->andReturn(false);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\TransportManagers')
            ->andReturn($form);

        $this->sut->getForm();
    }

    public function testGetFormWithoutFormAction()
    {
        $formActions = m::mock();
        $formActions->shouldReceive('has')->with('save')->andReturn(true);
        $formActions->shouldReceive('remove')->once()->with('save');

        $formActions->shouldReceive('has')->with('cancel')->andReturn(false);
        $formActions->shouldReceive('remove')->never()->with('cancel');

        $formActions->shouldReceive('has')->with('saveAndContinue')->andReturn(false);
        $formActions->shouldReceive('remove')->never()->with('saveAndContinue');

        $form = m::mock();
        $form->shouldReceive('has')->with('form-actions')->andReturn(true);
        $form->shouldReceive('get')->with('form-actions')->andReturn($formActions);

        $this->formHelper->shouldReceive('createForm')->once()
            ->with('Lva\TransportManagers')
            ->andReturn($form);

        $this->sut->getForm();
    }
}
