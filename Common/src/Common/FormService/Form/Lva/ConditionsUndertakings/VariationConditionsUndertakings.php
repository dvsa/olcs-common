<?php

namespace Common\FormService\Form\Lva\ConditionsUndertakings;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Conditions Undertakings
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationConditionsUndertakings extends AbstractConditionsUndertakings
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
