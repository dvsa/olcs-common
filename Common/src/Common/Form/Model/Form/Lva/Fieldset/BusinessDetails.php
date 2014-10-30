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
     *     "route": "lva-application/business_type"
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
     * @Form\Attributes({"class":"medium"})
     * @Form\Options({"label":"application_your-business_business-details.data.company_name"})
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     */
    public $name = null;

    /**
     * @Form\Options({"label": "application_your-business_business-details.data.trading_names_optional"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\TradingNames")
     */
    public $tradingNames = null;

    /**
     * @Form\Options({"label": "application_your-business_business-details.data.registered_address"})
     * @Form\ComposedObject("Common\Form\Model\Form\Lva\Fieldset\RegisteredAddress")
     */
    public $registeredAddress = null;
}
