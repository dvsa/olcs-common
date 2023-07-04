<?php

namespace Common\FormService\Form\Continuation;

use Common\Form\Form;
use Common\Form\Model\Form\Continuation\Declaration as FormModel;
use Common\RefData;
use Common\Service\Helper\FormHelperService;
use Common\Service\Helper\TranslationHelperService;
use Common\Service\Helper\UrlHelperService;
use Common\Service\Script\ScriptFactory;

/**
 * Declaration Form service
 */
class Declaration
{
    /** @var Form */
    private $form;

    /** @var array */
    private $continuationDetailData = [];

    private TranslationHelperService $translator;
    private ScriptFactory $scriptFactory;
    private UrlHelperService $urlHelper;
    protected FormHelperService $formHelper;

    public function __construct(
        FormHelperService $formHelper,
        TranslationHelperService $translator,
        ScriptFactory $scriptFactory,
        UrlHelperService $urlHelper
    ) {
        $this->formHelper = $formHelper;
        $this->translator = $translator;
        $this->scriptFactory = $scriptFactory;
        $this->urlHelper = $urlHelper;
    }

    /**
     * Get form
     *
     * @param array $continuationDetailData Continuation detail data
     *
     * @return \Common\Form
     */
    public function getForm(array $continuationDetailData = [])
    {
        $this->form = $this->formHelper->createForm(FormModel::class);
        $this->continuationDetailData = $continuationDetailData;

        $this->updateReviewElement();
        $this->updateDeclarationElement();
        $this->updateFormBasedOnDisableSignatureSetting();
        $this->updateFormActions();
        $this->updateFormSignature();

        $this->populateForm();

        return $this->form;
    }

    /**
     * Update form with signature details
     *
     * @return void
     */
    private function updateFormSignature()
    {
        // if form signed, then display signature details
        if (
            !empty($this->continuationDetailData['signature']['name'])
            && !empty($this->continuationDetailData['signature']['date'])
            && $this->continuationDetailData['signatureType']['id'] === RefData::SIGNATURE_TYPE_DIGITAL_SIGNATURE
        ) {
            $signedBy = $this->continuationDetailData['signature']['name'];
            $signedDate = new \DateTime($this->continuationDetailData['signature']['date']);

            // Update the form HTML with details name of person who signed
            /** @var \Common\Service\Helper\TranslationHelperService $translator */
            $this->form->get('signatureDetails')->get('signature')->setValue(
                $this->translator->translateReplace('undertakings_signed', [$signedBy, $signedDate->format(\DATE_FORMAT)])
            );
            $this->formHelper->remove($this->form, 'form-actions->sign');
            $this->formHelper->remove($this->form, 'content');
        } else {
            $this->formHelper->remove($this->form, 'signatureDetails');
            if ($this->continuationDetailData['disableSignatures'] === false) {
                $this->scriptFactory->loadFiles(['continuation-declaration']);
            }
        }
    }

    /**
     * Populate the form with values
     *
     * @return void
     */
    private function populateForm()
    {
        $this->form->get('version')->setValue($this->continuationDetailData['version']);
    }

    /**
     * Update the form actions
     *
     * @return void
     */
    private function updateFormActions()
    {
        if ($this->continuationDetailData['disableSignatures'] === true) {
            $this->formHelper->remove($this->form, 'form-actions->sign');
        }

        if ($this->continuationDetailData['hasOutstandingContinuationFee'] === true) {
            $this->formHelper->remove($this->form, 'form-actions->submit');
        } else {
            $this->formHelper->remove($this->form, 'form-actions->submitAndPay');
        }
    }

    /**
     * Update the declaration section
     *
     * @return void
     */
    private function updateDeclarationElement()
    {
        // set the declaration bullet point content from API data
        $this->form->get('content')->get('declaration')->setValue($this->continuationDetailData['declarations']);

        $declarationDownload = $this->translator->translateReplace(
            'undertakings_declaration_download',
            [
                $this->urlHelper->fromRoute('continuation/declaration/print', [], [], true),
                $this->translator->translate('print-declaration-form'),
            ]
        );
        $this->form->get('content')->get('declarationDownload')->setAttribute('value', $declarationDownload);
    }

    /**
     * Update the review section
     *
     * @return void
     */
    private function updateReviewElement()
    {
        if (!isset($this->continuationDetailData['organisationTypeId'])) {
            return;
        }
        // Chnage the review text dependant on organisation type
        $map = [
            RefData::ORG_TYPE_SOLE_TRADER => 'application.review-declarations.review.business-owner',
            RefData::ORG_TYPE_OTHER => 'application.review-declarations.review.person',
            RefData::ORG_TYPE_PARTNERSHIP => 'application.review-declarations.review.partner',
        ];
        if (isset($map[$this->continuationDetailData['organisationTypeId']])) {
            $this->updateReviewPersonName($map[$this->continuationDetailData['organisationTypeId']]);
        }
    }

    /**
     * Update the form dependant on whether Verify is enabled
     *
     * @return void
     */
    private function updateFormBasedOnDisableSignatureSetting()
    {
        if ($this->continuationDetailData['disableSignatures'] === true) {
            // remove options radio, sign button, checkbox, enable print sign and return fieldset
            $this->formHelper->remove($this->form, 'content->signatureOptions');
            $this->formHelper->remove($this->form, 'content->declarationForVerify');
        } else {
            $this->formHelper->remove($this->form, 'content->disabledReview');
        }
    }

    /**
     * Update the review section text with the correct name
     *
     * @param string $name Name/key to use as the review text token
     *
     * @return void
     */
    private function updateReviewPersonName($name)
    {
        /** @var \Common\Form\Elements\Types\HtmlTranslated $element */
        $element = $this->form->get('content')->get('review');
        $element->setTokens([$name]);
    }
}
