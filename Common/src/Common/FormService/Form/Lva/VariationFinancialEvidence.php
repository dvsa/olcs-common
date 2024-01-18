<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Laminas\Validator\ValidatorPluginManager;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * Variation Financial Evidence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationFinancialEvidence extends FinancialEvidence
{
    protected FormHelperService $formHelper;
    protected AuthorizationService $authService;
    protected UrlHelperService $urlHelper;
    protected TranslationHelperService $translator;
    protected ValidatorPluginManager $validatorPluginManager;


    public function __construct(
        FormHelperService $formHelper,
        AuthorizationService $authService,
        TranslationHelperService $translator,
        UrlHelperService $urlHelper,
        ValidatorPluginManager $validatorPluginManager
    ) {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
        $this->validatorPluginManager = $validatorPluginManager;
    }

    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');

        return parent::alterForm($form);
    }
}
