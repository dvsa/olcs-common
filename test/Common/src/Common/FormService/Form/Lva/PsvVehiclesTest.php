<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\PsvDiscs;
use Laminas\Form\Form;
use Mockery as m;
use Common\FormService\Form\Lva\PsvVehicles;
use ZfcRbac\Service\AuthorizationService;

/**
 * Psv Vehicles Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
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
        // Mocks
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
