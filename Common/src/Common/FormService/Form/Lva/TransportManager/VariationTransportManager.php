<?php

namespace Common\FormService\Form\Lva\TransportManager;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationTransportManager extends AbstractTransportManager
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
        $form = parent::alterForm($form);

        $this->removeFormAction($form, 'saveAndContinue');

        return $form;
    }
}
