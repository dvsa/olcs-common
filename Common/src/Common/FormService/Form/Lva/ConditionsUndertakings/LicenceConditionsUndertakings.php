<?php

namespace Common\FormService\Form\Lva\ConditionsUndertakings;

use Common\Service\Helper\FormHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * Licence Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceConditionsUndertakings extends AbstractConditionsUndertakings
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }

    protected function alterForm($form)
    {
        parent::alterForm($form);

        $this->removeStandardFormActions($form);

        return $form;
    }
}
