<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;

/**
 * Licence Variation Vehicles Form
 * - Common logic between goods/psv licence/variation vehicles forms
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVariationVehicles
{
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper)
    {
        $this->formHelper = $formHelper;
    }

    public function alterForm($form)
    {
        $this->formHelper->remove($form, 'data->hasEnteredReg');
        $this->formHelper->remove($form, 'data->notice');
    }
}
