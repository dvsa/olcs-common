<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Business details fieldset
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusinessDetails
{

    /**
     * @Form\Options({"label":"application_your-business_business-details.data.company_number"})
     * @Form\Type("Common\Form\Elements\Types\CompanyNumber")
     */
    public $companyNumber = null;

    /**
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({"label":"application_your-business_business-details.data.company_name"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $name = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TradingNames")
     * @Form\Attributes({"class": "add-another"})
     * @Form\Options({
     *     "label": "application_your-business_business-details.data.trading_names_optional"
     * })
     */
    public $tradingNames = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","class":"extra-long"})
     * @Form\Options({
     *     "label": "Nature of business",
     *     "error-message" : "businessDetails_natureOfBusiness-error",
     *     "label_attributes": {
     *         "aria-label": "Enter the nature of your business"
     *     },
     *     "help-block": "Please select a nature of business"
     * })
     * @Form\Validator({"name": "Zend\Validator\NotEmpty"})
     */
    public $natureOfBusiness = null;
}
