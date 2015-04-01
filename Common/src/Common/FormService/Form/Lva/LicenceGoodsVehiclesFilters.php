<?php

/**
 * Licence Goods Vehicles Filters Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Goods Vehicles Filters  Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesFilters extends CommonGoodsVehiclesFilters
{
    public function getForm()
    {
        $form = parent::getForm();

        $this->getFormHelper()->remove($form, 'specified');

        return $form;
    }
}
