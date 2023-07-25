<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * Licence Taxi Phv
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTaxiPhv extends TaxiPhv
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }
    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form
     * @return \Laminas\Form\Form
     */
    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
