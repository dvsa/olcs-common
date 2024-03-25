<?php

namespace Common\FormService\Form\Lva\CommunityLicences;

use Common\Service\Helper\FormHelperService;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Community Licences
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceCommunityLicences extends AbstractCommunityLicences
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
