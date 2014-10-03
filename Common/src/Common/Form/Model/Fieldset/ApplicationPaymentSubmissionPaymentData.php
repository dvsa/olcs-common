<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 * @Form\Options({
 *     "label": "Secure payment information",
 *     "hint": "To submit your application, please enter your card details below"
 * })
 */
class ApplicationPaymentSubmissionPaymentData
{

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "Card types",
     *     "value_options": {
     *         "Visa Credit",
     *         "Visa Debit",
     *         "MasterCard"
     *     },
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category"
     * })
     * @Form\Type("\Zend\Form\Element\Select")
     */
    public $cardTypes = null;

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({"label":"Name (as it appears on your card)"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $name = null;

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({"label":"Card number (No dashes or spaces)"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $cardNumber = null;

    /**
     * @Form\Attributes({
     *     "value": "<select><option>Please select</option></select>n                  
     *          <select><option>Please select</option></select>"
     * })
     * @Form\Options({"label":"Expiry date"})
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $expiryDate = null;

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({"label":"Security code"})
     * @Form\Required(false)
     * @Form\Type("Text")
     */
    public $securityCode = null;

    /**
     * @Form\Attributes({
     *     "value": "<div class="highlight-box">Fee payable for the application
     * <h2>&pound;254</h2>n                                After submitting your
     * application no further changes can be made</div>"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $amount = null;


}

