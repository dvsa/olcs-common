<?php

namespace CommonTest\Controller\Lva\Traits\Stubs;

use Common\Controller\Lva\Traits\LicenceOperatingCentresControllerTrait;
use Zend\Mvc\Controller\AbstractActionController;

/**
 * Concrete stub to let us test the common licence LVA behaviour wrapped up in a trait
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class LicenceOperatingCentresControllerTraitStub extends AbstractActionController
{
    use LicenceOperatingCentresControllerTrait;

    public function callAddAction()
    {
        return $this->addAction();
    }

    public function callDeleteAction()
    {
        return $this->deleteAction();
    }
}
