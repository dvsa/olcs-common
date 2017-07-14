<?php

namespace Common\FormService\Form\Continuation;

use Common\Form\Form;
use Common\FormService\Form\AbstractFormService;
use Common\Form\Model\Form\Continuation\Declaration as FormModel;
use Common\RefData;
use Common\Service\Helper\FormHelperService;

/**
 * Declaration Form service
 */
class Declaration extends AbstractFormService
{
    /** @var Form */
    private $form;

    /** @var array */
    private $continuationDetailData = [];

    /**
     * Get form
     *
     * @param array $continuationDetailData Continuation detail data
     *
     * @return \Common\Form
     */
    public function getForm(array $continuationDetailData = [])
    {
        $this->form = $this->getFormHelper()->createForm(FormModel::class);
        $this->continuationDetailData = $continuationDetailData;

        $this->updateReviewElement();
        $this->updateDeclarationElement();
        $this->updateFormBasedOnDisableSignatureSetting();
        $this->updateFormActions();

        $this->populateForm();

        return $this->form;
    }

    /**
     * Populate the form with values
     *
     * @return void
     */
    private function populateForm()
    {
        /** @var \Common\Service\Helper\FormHelperService $formHelper */
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        $this->form->get('version')->setValue($this->continuationDetailData['version']);
    }

    /**
     * Update the form actions
     *
     * @return void
     */
    private function updateFormActions()
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if ($this->continuationDetailData['disableSignatures'] === true) {
            $formHelper->remove($this->form, 'form-actions->sign');
        }

        if ($this->continuationDetailData['hasOutstandingContinuationFee'] === true) {
            $formHelper->remove($this->form, 'form-actions->submit');
        } else {
            $formHelper->remove($this->form, 'form-actions->submitAndPay');
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

        // set the Print/download link
        /** @var \Zend\Mvc\Controller\Plugin\Url $urlControllerPlugin */
        $urlControllerPlugin = $this->getServiceLocator()->get('ControllerPluginManager')->get('url');
        $translator = $this->getServiceLocator()->get('Helper\Translation');
        $declarationDownload = $translator->translateReplace(
            'undertakings_declaration_download',
            [
                // @todo This page/url needs updating as part of OLCS-16481
                $urlControllerPlugin->fromRoute('continuation/declaration', [], [], true),
                $translator->translate('print-declaration-form'),
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
        /** @var FormHelperService $this->formHelper */
        $formHelper = $this->getServiceLocator()->get('Helper\Form');
        if ($this->continuationDetailData['disableSignatures'] === true) {
            // remove options radio, sign button, checkbox, enable print sign and return fieldset
            $formHelper->remove($this->form, 'content->signatureOptions');
            $formHelper->remove($this->form, 'content->declarationForVerify');
        } else {
            $formHelper->remove($this->form, 'content->disabledReview');
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
