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
     *      "fieldset-attributes": {
     *          "class": "checkbox"
     *      },
     *     "label": "application_your-business_business-type.data.type",
     *     "disable_inarray_validator": false,
     *     "category": "org_type",
     *     "exclude": {"org_t_ir"}
     * })
     * @Form\Type("DynamicRadio")
     */
    public $type = null;
}
