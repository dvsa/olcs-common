<?php

/**
 * Application Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Application Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehiclesVehicle extends AbstractGoodsVehiclesVehicle
{
    protected $lva = 'application';

    protected function alterForm($form, $params)
    {
        $dataFieldset = $form->get('licence-vehicle');
        $this->getFormHelper()->disableDateElement($dataFieldset->get('specifiedDate'));
        $this->getFormHelper()->disableDateElement($dataFieldset->get('removalDate'));

        parent::alterForm($form, $params);
    }
}
