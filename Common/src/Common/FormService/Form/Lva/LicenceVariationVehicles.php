<?php

/**
 * Licence Variation Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;

/**
 * Licence Variation Vehicles Form
 * - Common logic between goods/psv licence/variation vehicles forms
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVariationVehicles extends AbstractFormService
{
    public function alterForm($form)
    {
        $formHelper = $this->getFormHelper();

        $formHelper->remove($form, 'data->hasEnteredReg');
        $formHelper->remove($form, 'data->notice');
    }
}
