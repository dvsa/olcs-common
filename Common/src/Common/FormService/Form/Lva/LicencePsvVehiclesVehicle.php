<?php

/**
 * Licence Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

/**
 * Licence Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePsvVehiclesVehicle extends AbstractPsvVehiclesVehicle
{
    protected function alterForm($form, $params)
    {
        if ($params['mode'] == 'edit') {
            $form->get('licence-vehicle')->get('specifiedDate')->setShouldCreateEmptyOption(false);
        }

        parent::alterForm($form, $params);
    }
}
