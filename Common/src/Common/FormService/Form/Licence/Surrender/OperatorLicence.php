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
    protected function alterForm($form)
    {
        $form->get('form-actions')->get('submit')->setLabel('Save and continue');
    }
}
