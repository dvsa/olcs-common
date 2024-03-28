<?php

namespace Common\FormService\Form\Lva;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Application Goods Vehicles Form
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationGoodsVehicles extends AbstractGoodsVehicles
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    private FormServiceManager $formServiceLocator;

    public function __construct(
        FormHelperService $formHelper,
        AuthorizationService $authService,
        FormServiceManager $formServiceLocator
    ) {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
        $this->formServiceLocator = $formServiceLocator;
    }

    protected function alterForm($form)
    {
        $this->formServiceLocator->get('lva-application')->alterForm($form);
    }
}
