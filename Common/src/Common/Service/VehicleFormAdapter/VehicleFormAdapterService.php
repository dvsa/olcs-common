<?php

/**
 * VehicleFormAdapter Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Common\Service\VehicleFormAdapter;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * VehicleFormAdapter Service
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class VehicleFormAdapterService implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function alterForm($form)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $formHelper->remove($form, 'licence-vehicle->specifiedDate');
        $formHelper->remove($form, 'licence-vehicle->removalDate');
        $formHelper->remove($form, 'licence-vehicle->discNo');

        return $form;
    }
}
