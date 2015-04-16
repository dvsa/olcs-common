<?php

/**
 * Licence Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehicles extends AbstractGoodsVehicles
{
    protected function alterForm($form, $isCrudPressed)
    {
        parent::alterForm($form, $isCrudPressed);

        $this->getFormServiceLocator()->get('lva-licence')->alterForm($form);
        $this->getFormServiceLocator()->get('lva-licence-variation-vehicles')->alterForm($form);
    }
}
