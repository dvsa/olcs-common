<?php

namespace Common\Form\Model\Form\Continuation\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Attributes({"class":"actions-container"})
 */
class DeclarationFormActions
{
    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large","id":"sign"})
     * @Form\Options({"label": "application.review-declarations.sign-button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $sign = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large","id":"submitAndPay"})
     * @Form\Options({"label": "submitandpay.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submitAndPay = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary large","id":"submit"})
     * @Form\Options({"label": "submitapplication.button"})
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionButton")
     */
    public $submit = null;
}
