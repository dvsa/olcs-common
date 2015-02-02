<?php

/**
 * Common Application Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Zend\Mvc\Controller\AbstractActionController;
use Common\Controller\Lva\Traits\PsvVariationControllerTrait;

/**
 * Common Application Controller Trait Stub
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVariationControllerTraitStub extends AbstractActionController
{
    use PsvVariationControllerTrait;

    public function callShowVehicle(array $licenceVehicle, array $filters = [])
    {
        return $this->showVehicle($licenceVehicle, $filters);
    }
}
