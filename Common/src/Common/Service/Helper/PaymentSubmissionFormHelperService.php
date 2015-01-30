<?php

/**
 * Payment Submission Form Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Helper;

use Zend\Form\Form;

/**
 * Payment Submission Form Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class PaymentSubmissionFormHelperService extends AbstractHelperService
{
    /**
     * The Payment Submission form must be altered according to context
     *
     * @param Zend\Form\Form $form
     * @param string $actionUrl
     * @param array $fee
     * @param boolean $canSubmit
     * @param boolean $enabled
     * @param string $actionUrl
     */
    public function updatePaymentSubmissonForm(
        Form $form,
        $actionUrl,
        array $fee = null,
        $visible = false,
        $enabled = false
    ) {
        $helper = $this->getServiceLocator()->get('Helper\Form');

        if ($visible) {
            if ($fee) {
                // show fee amount
                $feeAmount = number_format($fee['amount'], 2);
                $form->get('amount')->setTokens([0 => $feeAmount]);
            } else {
                // if no fee, change submit button text
                $helper->remove($form, 'amount');
                $form->get('submitPay')->setLabel('submit-application.button');
            }
            if ($enabled) {
                $form->setAttribute('action', $actionUrl);
            } else {
                $helper->disableElement($form, 'submitPay');
            }
        } else {
            // remove submit button and amount
            $helper->remove($form, 'amount');
            $helper->remove($form, 'submitPay');
        }
    }
}
