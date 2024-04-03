<?php

namespace Common\FormService\Form\Lva\People\SoleTrader;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Lva\PeopleLvaService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceSoleTrader extends AbstractSoleTrader
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    protected PeopleLvaService $peopleLvaService;

    private FormServiceManager $formServiceLocator;

    public function __construct(
        FormHelperService $formHelper,
        AuthorizationService $authService,
        PeopleLvaService $peopleLvaService,
        FormServiceManager $formServiceLocator
    ) {
        $this->formServiceLocator = $formServiceLocator;
        parent::__construct($formHelper, $authService, $peopleLvaService);
    }

    protected function alterForm($form, array $params)
    {
        $form = parent::alterForm($form, $params);

        $this->formServiceLocator->get('lva-licence')->alterForm($form);

        return $form;
    }
}
