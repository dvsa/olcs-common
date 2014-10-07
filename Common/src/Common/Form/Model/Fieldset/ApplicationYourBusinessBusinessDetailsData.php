<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 */
class ApplicationYourBusinessBusinessDetailsData
{

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","disabled":true,"class":"inline"})
     * @Form\Options({
     *     "label": "application_your-business_business-type.data.type",
     *     "value": "defendant_type.operator",
     *     "disable_inarray_validator": false,
     *     "service_name": "staticList",
     *     "category": "business_types"
     * })
     * @Form\Required(false)
     * @Form\Type("DynamicSelect")
     */
    public $type = null;

    /**
     * @Form\Attributes({"type":"submit","class":"action--primary"})
     * @Form\Options({
     *     "label": "Edit",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-10",
     *     "route": "Application/YourBusiness/BusinessType"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\ActionLink")
     */
    public $editBusinessType = null;

    /**
     * @Form\Options({"label":"application_your-business_business-details.data.company_number"})
     * @Form\Type("Common\Form\Elements\Types\CompanyNumber")
     */
    public $companyNumber = null;

    /**
     * @Form\Options({"label":"application_your-business_business-details.data.company_name"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $name = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-5",
     *     "help-block": "Between 2 and 50 characters.",
     *     "label":
     * "application_your-business_business-details.data.trading_names_optional"
     * })
     * @Form\ComposedObject("Common\Form\Model\Fieldset\TradingNames")
     */
    public $tradingNames = null;

    /**
     * @Form\Attributes({"id":"","class":"long"})
     * @Form\Options({
     *     "label":
     * "application_your-business_business-details.data.trading_names_optional",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box."
     * })
     * @Form\Type("\Zend\Form\Element\Textarea")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Filter({"name":"Zend\Filter\StringToLower"})
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":10,"max":100}})
     */
    public $tradingNamesReview = null;


}

