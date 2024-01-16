<?php

namespace Common\FormService\Form\Lva\TypeOfLicence;

use Common\FormService\FormServiceManager;
use Common\Service\Helper\FormHelperService;
use Laminas\Form\Form;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Licence Type Of Licence
 */
class LicenceTypeOfLicence extends AbstractTypeOfLicence
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;
    protected FormServiceManager $formServiceLocator;

    public function __construct(FormHelperService $formHelper, AuthorizationService $authService, FormServiceManager $formServiceLocator)
    {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
        $this->formServiceLocator = $formServiceLocator;
    }
    protected function alterForm(Form $form, $params = [])
    {
        parent::alterForm($form, $params);

        $this->formServiceLocator->get('lva-licence')->alterForm($form);

        $this->lockElements($form, $params);
    }

    protected function allElementsLocked(Form $form)
    {
        $this->removeStandardFormActions($form);
        $this->addBackToOverviewLink($form, 'licence');
    }
}
