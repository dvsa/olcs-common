<?php

/**
 * Variation Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationPsvVehiclesVehicle extends AbstractPsvVehiclesVehicle
{
    protected function alterForm($form, $params)
    {
        $dataFieldset = $form->get('licence-vehicle');
        $this->getFormHelper()->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->getFormHelper()->disableDateElement($dataFieldset->get('removalDate'));

        parent::alterForm($form, $params);
    }
}
