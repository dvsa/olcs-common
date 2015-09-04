<?php

/**
 * Psv Vehicles Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace CommonTest\FormService\Form\Lva;

use Mockery as m;
use Common\FormService\Form\Lva\PsvVehicles;

/**
 * Psv Vehicles Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PsvVehiclesTest extends AbstractLvaFormServiceTestCase
{
    protected $classToTest = PsvVehicles::class;

    protected $formName = 'Lva\PsvVehicles';

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
