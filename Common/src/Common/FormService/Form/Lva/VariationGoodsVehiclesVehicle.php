<?php

/**
 * Variation Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehiclesVehicle extends AbstractGoodsVehiclesVehicle
{
    protected $lva = 'variation';

    protected function alterForm($form, $params)
    {
        // Common with applications
        $dataFieldset = $form->get('licence-vehicle');
        $this->getFormHelper()->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->getFormHelper()->disableDateElement($dataFieldset->get('removalDate'));

        parent::alterForm($form, $params);
    }
}
