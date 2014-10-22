<?php

/**
 * External Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\Controller\Lva\Traits;

use Zend\Form\Form;

/**
 * External Vehicles Controller Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait ExternalVehiclesControllerTrait
{
    protected function alterVehicleForm(Form $form)
    {
        $this->getServiceLocator()->get('Helper\Form')
            ->remove($form, 'licence-vehicle->receivedDate');
    }
}
