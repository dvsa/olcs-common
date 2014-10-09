<?php

namespace Common\Form\Model\Form;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("application_payment-submission_payment")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class ApplicationPaymentSubmissionPayment
{

    /**
     * @Form\Name("data")
     * @Form\Options({
     *     "label": "Secure payment information",
     *     "hint": "To submit your application, please enter your card details below"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\ApplicationPaymentSubmissionPaymentData")
     */
    public $data = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Fieldset\JourneyButtons")
     */
    public $formActions = null;


}

