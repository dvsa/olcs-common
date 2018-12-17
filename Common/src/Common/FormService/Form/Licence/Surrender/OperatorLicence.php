<?php

namespace Common\FormService\Form\Licence\Surrender;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;
use \Olcs\Form\Model\Form\Surrender\OperatorLicence as OperatorLicenceForm;
use Common\Form\Form;

class OperatorLicence extends AbstractFormService
{
    /**
     * Get form
     *
     * @return Form
     */
    public function getForm()
    {
        $form = $this->getFormHelper()->createForm(OperatorLicenceForm::class);

        $this->alterForm($form);

        return $form;
    }

    public function setStatus($form, $apiData)
    {
        $mapStatusToRadio = [
            RefData::SURRENDER_DOC_STATUS_DESTROYED => 'possession',
            RefData::SURRENDER_DOC_STATUS_STOLEN => 'stolen',
            RefData::SURRENDER_DOC_STATUS_LOST => 'lost'
        ];

        $licenceDocumentStatus = $apiData["licenceDocumentStatus"]["id"];

        $var = $mapStatusToRadio[$licenceDocumentStatus];

        $formRadio = $form->get('operatorLicenceDocument')->get('licenceDocument');
        $formRadio->setValue($var);

        return $form;
    }

    /**
     * Alter form
     *
     * @param Form $form form
     *
     * @return void
     */
    private function alterForm($form)
    {
        $form->get('form-actions')->get('submit')->setLabel('Save and continue');
    }
}
