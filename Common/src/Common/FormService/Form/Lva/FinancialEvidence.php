<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use ZfcRbac\Service\AuthorizationService;

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractLvaFormService
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

    /**
     * Get Form
     *
     * @param \Laminas\Http\Request $request Request
     *
     * @return \Laminas\Form\Form
     */
    public function getForm(\Laminas\Http\Request $request)
    {
        $form = $this->formHelper->createFormWithRequest('Lva\FinancialEvidence', $request);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Laminas\Form\Form $form Form
     *
     * @return void
     */
    protected function alterForm($form)
    {
        $evidenceFieldset = $form->get('evidence');
        $evidenceFieldset->get('uploadNowRadio')->setName('uploadNow');
        $evidenceFieldset->get('uploadLaterRadio')->setName('uploadNow');
        $evidenceFieldset->get('sendByPostRadio')->setName('uploadNow');
        $this->formHelper->remove($form, 'evidence->uploadNow');

        $evidenceHint = $this->translator->translateReplace(
            'lva-financial-evidence-evidence.hint',
            [
                $this->urlHelper->fromRoute('guides/guide', ['guide' => 'financial-evidence'], [], true),
            ]
        );
        $evidenceFieldset->setOption('hint', $evidenceHint);
    }
}
