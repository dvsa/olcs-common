<?php

/**
 * Licence Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Goods Vehicles Vehicle Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceGoodsVehiclesVehicle extends AbstractGoodsVehiclesVehicle
{
    protected $lva = 'licence';

    protected function alterForm($form, $params)
    {
        if ($params['mode'] === 'edit') {
            $form->get('licence-vehicle')->get('specifiedDate')->setShouldCreateEmptyOption(false);
        }

        parent::alterForm($form, $params);
    }
}
