<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 * @Form\Options({"label":"application_your-business_business-type.data"})
 */
class ApplicationYourBusinessBusinessTypeData
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

