<?php

namespace Common\FormService\Form\Lva;

use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Common\Validator\ValidateIf;
use Laminas\Validator\ValidatorPluginManager;
use LmcRbacMvc\Service\AuthorizationService;

/**
 * FinancialEvidence Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractLvaFormService
{
    public function __construct(protected FormHelperService $formHelper, protected AuthorizationService $authService, protected TranslationHelperService $translator, protected UrlHelperService $urlHelper, protected ValidatorPluginManager $validatorPluginManager)
    {
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

        $inputFilter = $form->getInputFilter();

        $evidenceInputFilter = $inputFilter->get('evidence');

        $evidenceInputFilter->get('uploadNowRadio')->setRequired(false);
        $evidenceInputFilter->get('uploadLaterRadio')->setRequired(false);
        $evidenceInputFilter->get('sendByPostRadio')->setRequired(false);

        $uploadedFileCountInput = $evidenceInputFilter->get('uploadedFileCount');
        $validateIfValidator = $this->validatorPluginManager->get(ValidateIf::class);
        $validateIfValidator->setOptions([
            'context_field' => 'uploadNowRadio',
            'context_values' => ['1'],
            'validators' => [
                [
                    'name' => \Common\Validator\FileUploadCount::class,
                    'options' => [
                        'min' => 1,
                        'message' => 'lva-financial-evidence-upload.required',
                    ],
                ],
            ],
        ]);

        $uploadedFileCountInput->getValidatorChain()->attach($validateIfValidator);
    }
}
