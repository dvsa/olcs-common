<?php

namespace Common\FormService\Form\Continuation;

use Common\FormService\Form\AbstractFormService;
use Common\Service\Helper\FormHelperService;
use Common\Form\Model\Form\Continuation\Payment as PaymentForm;
use Common\Form\Form;
use Common\RefData;

/**
 * Continuation fee payment form
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Payment extends AbstractFormService
{
    /**
     * Get form
     *
     * @param array $data continuation detail data
     *
     * @return Form
     */
    public function getForm($data)
    {
        $form = $this->getFormHelper()->createForm(PaymentForm::class);

        $this->alterForm($form, $data);

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
    protected function alterForm($form, $data)
    {
        if (isset($data['disableCardPayments']) && $data['disableCardPayments'] === true) {
            $formActions = $form->get('form-actions');
            $formActions->remove('pay');
            $cancelButton = $form->get('form-actions')->get('cancel');
            $cancelButton->setLabel('back-to-fees');
            $cancelButton->setAttribute('class', 'action--tertiary large');
            $this->getServiceLocator()->get('Helper\Guidance')->append('selfserve-card-payments-disabled');
        }
    }
}
