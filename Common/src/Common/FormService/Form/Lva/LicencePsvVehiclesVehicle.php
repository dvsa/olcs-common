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

        $this->getFormHelper()->enableDateTimeElement($form->get('licence-vehicle')->get('specifiedDate'));

        parent::alterForm($form, $params);

        if ($params['isRemoved']) {
            if ($params['location'] === 'external') {
                $form->get('form-actions')->remove('submit');
            } else {
                $this->getFormHelper()->enableDateElement($form->get('licence-vehicle')->get('removalDate'));
                $form->get('licence-vehicle')->get('removalDate')->setShouldCreateEmptyOption(false);
            }
        }
    }
}
