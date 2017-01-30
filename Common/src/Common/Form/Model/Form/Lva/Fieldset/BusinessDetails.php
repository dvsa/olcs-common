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
     * @Form\Attributes({"id":"companyNumber"})
     * @Form\Type("Common\Form\Elements\Types\CompanyNumber")
     */
    public $companyNumber = null;

    /**
     * @Form\Attributes({"class":"long"})
     * @Form\Options({"label":"application_your-business_business-details.data.company_name"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name":"Zend\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $name = null;

    /**
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TradingNames")
     * @Form\Attributes({"class": "add-another"})
     * @Form\Options({"label": "application_your-business_business-details.data.trading_names_optional"})
     */
    public $tradingNames = null;

    /**
     * @Form\Attributes({"id":"natureOfBusiness","placeholder":"","class":"extra-long"})
     * @Form\Options({
     *     "label":"Nature of business",
     *     "error-message":"businessDetails_natureOfBusiness-error",
     *     "label_attributes":{"aria-label": "businessDetails_natureOfBusiness-error"},
     *     "help-block":"Please select a nature of business"
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength", "options":{
     *     "min": 0, "max": 200
     * }})
     */
    public $natureOfBusiness = null;
}
