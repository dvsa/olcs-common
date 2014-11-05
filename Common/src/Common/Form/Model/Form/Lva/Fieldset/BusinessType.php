<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Business type fieldset
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class BusinessType
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_your-business_business-type.data.type",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "service_name": "staticList",
     *     "category": "business_types"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $type = null;
}
