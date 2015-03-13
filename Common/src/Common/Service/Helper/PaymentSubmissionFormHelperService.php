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
     * @param int $applicationId
     * @param boolean $canSubmit
     * @param boolean $enabled
     * @param string $actionUrl
     */
    public function updatePaymentSubmissonForm(
        Form $form,
        $actionUrl,
        $applicationId,
        $visible = false,
        $enabled = false
    ) {

        $formHelper = $this->getServiceLocator()->get('Helper\Form');

        if ($visible) {
            $fee = $this->getFee($applicationId);
            if ($fee) {
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

    /**
     * @param int $applicationId
     * @return float
     */
    protected function getFee($applicationId)
    {
        $processingService = $this->getServiceLocator()->get('Processing\Application');
        $applicationFee = $processingService->getApplicationFee($applicationId);

        $fee = 0;

        if ($applicationFee) {
            $fee += $applicationFee['amount'];
        }

        $category = $this->getServiceLocator()->get('Entity\Application')->getCategory($applicationId);
        if ($category === LicenceEntityService::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $interimFee = $processingService->getInterimFee($applicationId);

            if ($interimFee) {
                $fee += $interimFee['amount'];
            }
        }

        return $fee;
    }
}
