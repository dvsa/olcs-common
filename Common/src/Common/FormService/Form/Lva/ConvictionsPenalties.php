<?php

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;
use Zend\Form\Fieldset;

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ConvictionsPenalties extends AbstractFormService
{
    /**
     * get the form
     *
     * @param array $params parameters used for switching/configuring form
     *
     * @return \Common\Form\Form|\Zend\Form\Form
     */
    public function getForm(array $params = [])
    {
        $form = $this->getFormHelper()->createForm('Lva\ConvictionsPenalties');
        $this->alterForm($form, $params);
        return $form;
    }

    /**
     * Determine if form changes required as is director variation
     *
     * @param array $params parameters used for switching/configuring form
     *
     * @return bool
     */
    protected function isDirectorChange(array $params)
    {
        return isset($params['variationType']) &&
            $params['variationType'] === RefData::VARIATION_TYPE_DIRECTOR_CHANGE;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form   form
     * @param array           $params parameters used for switching/configuring form
     *
     * @return \Zend\Form\Form form
     *
     */
    protected function alterForm($form, array $params)
    {
        if ($this->isDirectorChange($params)) {
            $dataTable = $form->get('data');
            $this->alterFormQuestion($dataTable);
            $this->alterFormHeading($dataTable, $params);
            $this->alterFormButtons($form);
            $this->alterConfirmation($form);
            $this->getFormHelper()->remove($form, 'form-actions->save');
        }
        return $form;
    }

    /**
     * Alter the form standard buttons
     *
     * @param \Zend\Form\Form $form form
     *
     * @return void
     *
     */
    private function alterFormButtons($form)
    {
        $formActions = $form->get('form-actions');
        $formActions->get('saveAndContinue')->setLabel('Submit details');
    }

    /**
     * Alter the confirmation message
     *
     * @param \Zend\Form\Form $form form
     *
     * @return void
     */
    private function alterConfirmation($form)
    {
        $form->get('convictionsConfirmation')->setLabel('I agree to:');
        $form->get('convictionsConfirmation')
            ->get('convictionsConfirmation')
            ->setLabel('director-change-convictions-penalties-conformation');
    }

    /**
     * Set dynamic label based on organisation type
     *
     * @param \Zend\Form\Fieldset $dataTable fieldset for table
     * @param array               $params    parameters
     *
     * @return void
     */
    private function alterFormHeading($dataTable, array $params)
    {
        $label = $dataTable->getLabel();
        $dataTable->setLabel($label . '-' . $params['organisationType']);
    }

    /**
     * remove form question text
     *
     * @param \Zend\Form\Fieldset $dataTable data table
     *
     * @return void
     */
    private function alterFormQuestion($dataTable)
    {
        $question = $dataTable->get('question');
        $question->setLabel('');
    }
}
