<?php

namespace CommonTest\Common\FormService\Form\Lva;

use Mockery as m;
use Common\FormService\Form\Lva\PsvVehicles;
use ZfcRbac\Service\AuthorizationService;

class PsvVehiclesTest extends AbstractLvaFormServiceTestCase
{
    protected $classToTest = PsvVehicles::class;

    protected $formName = 'Lva\PsvVehicles';

    public function setUp(): void
    {
        $this->authService = m::mock(AuthorizationService::class);
        $this->classArgs = [$this->authService];
        parent::setUp();
    }

    public function testGetForm()
    {
        $mockForm = m::mock();

        $this->formHelper->shouldReceive('createForm')
            ->with($this->formName)
            ->andReturn($mockForm)
            ->shouldReceive('remove')
            ->once()
            ->with($mockForm, 'shareInfo');

        $form = $this->sut->getForm();

        $this->assertSame($mockForm, $form);
    }
}
