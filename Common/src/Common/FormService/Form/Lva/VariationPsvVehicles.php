<?php

/**
 * Variation Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvVehicles extends PsvVehicles
{
    protected function alterForm($form)
    {
        $this->removeStandardFormActions($form);
        return $form;
    }
}
