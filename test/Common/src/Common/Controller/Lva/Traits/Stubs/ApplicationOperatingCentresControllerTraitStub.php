<?php

/**
 * Application Operating Centres Trait Stub
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\ApplicationOperatingCentresControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Application Operating Centres Trait Stub
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ApplicationOperatingCentresControllerTraitStub extends AbstractActionController
{
    use ApplicationOperatingCentresControllerTrait;

    public function callCheckTrafficAreaAfterCrudAction($data)
    {
        return $this->checkTrafficAreaAfterCrudAction($data);
    }
}
