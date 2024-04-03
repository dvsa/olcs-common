<?php

namespace Common\FormService\Form\Lva\TransportManager;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Application Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTransportManager extends AbstractTransportManager
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
    }
}
