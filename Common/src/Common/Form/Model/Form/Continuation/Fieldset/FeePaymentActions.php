<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("fee-payment-actions")
 * @Form\Attributes({"class":"actions-container"})
 */
class FeePaymentActions
{
    /**
     * @Form\Attributes({"type":"submit","class":"action--primary action--external large"})
     * @Form\Options({"label": "continuation.payment.pay-and-submit"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $pay = null;
}
