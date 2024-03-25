<?php

namespace Common\FormService\Form\Lva\People\SoleTrader;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Common\Service\Lva\PeopleLvaService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Sole Trader
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationSoleTrader extends AbstractSoleTrader
{
    protected FormHelperService $formHelper;

    protected AuthorizationService $authService;

    protected PeopleLvaService $peopleLvaService;

    protected FormServiceManager $formServiceLocator;

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

        $this->formServiceLocator->get('lva-variation')->alterForm($form);

        return $form;
    }
}
