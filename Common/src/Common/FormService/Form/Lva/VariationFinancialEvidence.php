<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use ZfcRbac\Service\AuthorizationService;

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


    public function __construct(
        FormHelperService $formHelper,
        AuthorizationService $authService,
        TranslationHelperService $translator,
        UrlHelperService $urlHelper
    ) {
        $this->formHelper = $formHelper;
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->translator = $translator;
    }

    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');

        return parent::alterForm($form);
    }
}
