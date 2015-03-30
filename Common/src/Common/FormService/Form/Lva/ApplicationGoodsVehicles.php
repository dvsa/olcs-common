<?php

/**
 * Application Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Application Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehicles extends AbstractGoodsVehicles
{
    protected function alterForm($form, $isCrudPressed)
    {
        $this->getFormServiceLocator()->get('lva-application')->alterForm($form);

        parent::alterForm($form, $isCrudPressed);
    }
}
