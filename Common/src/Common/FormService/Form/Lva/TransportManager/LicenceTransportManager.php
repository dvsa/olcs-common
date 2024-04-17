<?php

namespace Common\FormService\Form\Lva\TransportManager;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Transport Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTransportManager extends AbstractTransportManager
{
    public function __construct(protected FormHelperService $formHelper, protected AuthorizationService $authService)
    {
    }

    protected function alterForm($form)
    {
        $form = parent::alterForm($form);

        $this->removeFormAction($form, 'saveAndContinue');

        return $form;
    }
}
