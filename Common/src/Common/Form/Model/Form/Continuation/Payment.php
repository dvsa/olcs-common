<?php

namespace Common\Form\Model\Form\Continuation;

use Zend\Form\Annotation as Form;

/**
 * @Form\Options({"prefer_form_input_filter":true})
 * @Form\Name("fee-payment")
 * @Form\Attributes({"method":"post"})
 * @Form\Type("Common\Form\Form")
 */
class Payment
{
    /**
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $amount = null;


    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\FeeStoredCards")
     */
    public $storedCards = null;

    /**
     * @Form\Name("form-actions")
     * @Form\Attributes({"class":"actions-container"})
     * @Form\ComposedObject("Common\Form\Model\Form\Continuation\Fieldset\FeePaymentActions")
     */
    public $formActions = null;
}
