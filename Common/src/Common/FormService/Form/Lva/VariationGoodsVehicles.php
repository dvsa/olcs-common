<?php

/**
 * Variation Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Variation Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationGoodsVehicles extends AbstractGoodsVehicles
{
    protected function alterForm($form)
    {
        $this->removeStandardFormActions($form);
        $this->getFormServiceLocator()->get('lva-licence-variation-vehicles')->alterForm($form);
    }
}
