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

        // we don't have any visible elements now but we need
        // to keep this fieldset to save existing logic
        $form->get('licence-vehicle')->setAttribute('class', 'visually-hidden');

        return $form;
    }
}
