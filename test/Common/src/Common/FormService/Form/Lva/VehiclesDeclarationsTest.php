<?php

namespace CommonTest\FormService\Form\Lva;

use Common\FormService\Form\Lva\VehiclesDeclarations;
use Laminas\Form\Form;
use Mockery as m;

/**
 * Vehicles Declarations Form Service Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class VehiclesDeclarationsTest extends AbstractLvaFormServiceTestCase
{
    protected $classToTest = VehiclesDeclarations::class;

    protected $formName = 'Lva\VehiclesDeclarations';
}
