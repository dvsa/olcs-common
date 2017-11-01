<?php

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\FormService\Form\Lva;

use Common\FormService\Form\AbstractFormService;
use Common\RefData;

/**
 * Convictions and Penalties Form
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ConvictionsPenalties extends AbstractFormService
{
    public function getForm(array $params = [])
    {
        $form = $this->getFormHelper()->createForm('Lva\ConvictionsPenalties');

        $this->alterForm($form, $params);

        return $form;
    }

    /**
     * Make form alterations
     *
     * @param \Zend\Form\Form $form
     *
     * @return \Zend\Form\Form
     */
    protected function alterForm($form, $params)
    {
        $dataTable = $form->get('data');
        $question = $dataTable->get('question');
        $question->setLabel('');



        $this->alterFormButtons($form);
        $form->get('convictionsConfirmation')->setLabel('I agree to:');
        $form->get('convictionsConfirmation')->get('convictionsConfirmation')->setLabel('tell the Traffic Commissioner immediately about any relevant offences that take place in the period between submitting this application and a decision being made.');
        $this->getFormHelper()->remove($form, 'form-actions->save');
        return $form;
    }

    /**
     * @param $form
     */
    protected function alterFormButtons($form)
    {
        $formActions = $form->get('form-actions');
        $formActions->get('saveAndContinue')->setLabel('Submit details');
    }
}
