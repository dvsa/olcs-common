<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Psv Vehicles Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicencePsvVehiclesVehicle extends AbstractPsvVehiclesVehicle
{
    protected FormServiceManager $formServiceLocator;
    protected FormHelperService $formHelper;

    public function __construct(FormHelperService $formHelper, FormServiceManager $formServiceLocator)
    {
        $this->formHelper = $formHelper;
        $this->formServiceLocator = $formServiceLocator;
    }

    protected function alterForm($form, $params)
    {
        if ($params['mode'] == 'edit') {
            $form->get('licence-vehicle')->get('specifiedDate')->setShouldCreateEmptyOption(false);
        }

        $this->formHelper->enableDateTimeElement($form->get('licence-vehicle')->get('specifiedDate'));

        parent::alterForm($form, $params);

        if ($params['isRemoved']) {
            if ($params['location'] === 'external') {
                $form->get('form-actions')->remove('submit');
            } else {
                $this->formHelper->enableDateElement($form->get('licence-vehicle')->get('removalDate'));
                $form->get('licence-vehicle')->get('removalDate')->setShouldCreateEmptyOption(false);
            }
        }
    }
}
