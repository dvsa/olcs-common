<?php

/**
 * Payment Submission Form Helper Service
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Common\Service\Helper;

use Common\Service\Entity\LicenceEntityService;
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
     * @param boolean $canSubmit
     * @param boolean $enabled
     * @param string $fee fee amount
     */
    public function updatePaymentSubmissonForm(Form $form, $actionUrl, $visible, $enabled, $fee)
    {
        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if ($visible) {
            if (floatval($fee) > 0) {
                // show fee amount
                $feeAmount = number_format($fee, 2);
                $translator = $this->getServiceLocator()->get('Helper\Translation');
                $form->get('amount')->setValue(
                    $translator->translateReplace(
                        'application.payment-submission.amount.value',
                        [$feeAmount]
                    )
                );
            } else {
                // if no fee, change submit button text
                $formHelper->remove($form, 'amount');
                $form->get('submitPay')->setLabel('submit-application.button');
            }

            // note, we don't set an action on the form if we're disabling
            // the submit button
            if ($enabled) {
                $form->setAttribute('action', $actionUrl);
            } else {
                $formHelper->disableElement($form, 'submitPay');
            }
        } else {
            // remove submit button and amount
            $formHelper->remove($form, 'amount');
            $formHelper->remove($form, 'submitPay');
        }
    }
}
