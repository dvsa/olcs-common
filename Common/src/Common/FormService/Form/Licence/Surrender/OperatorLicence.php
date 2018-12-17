<?php

namespace Common\FormService\Form\Licence\Surrender;

use Common\FormService\Form\AbstractFormService;
use \Olcs\Form\Model\Form\Surrender\OperatorLicence as OperatorLicenceForm;
use Common\Form\Form;

class OperatorLicence extends AbstractFormService
{
    /**
     * Get form
     *
     * @param array $data continuation detail data
     *
     * @return Form
     */
    public function getForm()
    {

        $form = $this->getFormHelper()->createForm(OperatorLicenceForm::class);

        $this->alterForm($form);

        return $form;
    }

    /**
     * Alter form
     *
     * @param Form  $form form
     * @param array $data data
     *
     * @return void
     */
    private function alterForm($form)
    {
        $form->get('form-actions')->get('submit')->setLabel('Save and continue');

    }

    public function setStatus($form, $apiData)
    {
        $licenceDocumentStatus = $apiData["licenceDocumentStatus"]["id"];

        $var = str_replace('doc_sts_', '', $licenceDocumentStatus);
        $var = $var == 'destroyed' ? 'possession' : $var;

        $formRadio = $form->get('operatorLicenceDocument')->get('licenceDocument');
        $formRadio->setValue($var);

        return $form;
    }
}
