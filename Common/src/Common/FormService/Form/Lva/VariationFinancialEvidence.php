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
    public function __construct(protected FormHelperService $formHelper, protected AuthorizationService $authService, protected TranslationHelperService $translator, protected UrlHelperService $urlHelper, protected ValidatorPluginManager $validatorPluginManager)
    {
    }

    protected function alterForm($form)
    {
        $this->removeFormAction($form, 'saveAndContinue');

        parent::alterForm($form);
    }
}
