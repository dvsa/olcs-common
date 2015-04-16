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
    protected function alterForm($form, $isCrudPressed)
    {
        parent::alterForm($form, $isCrudPressed);

        $this->getFormServiceLocator()->get('lva-variation')->alterForm($form);
        $this->getFormServiceLocator()->get('lva-licence-variation-vehicles')->alterForm($form);
    }
}
