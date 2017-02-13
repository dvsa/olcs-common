<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-safety-providers-data")
 */
class SafetyProvidersData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox"
     *      },
     *     "label": "application_vehicle-safety_safety-sub-action.data.isExternal",
     *     "value_options": {
     *         {
     *             "value": "N",
     *             "label": "application_vehicle-safety_safety-sub-action.data.isExternal.option.no",
     *             "label_attributes": {
     *                 "aria-label": "Who'll carry out the safety inspections? An owner or employee of the business"
     *             }
     *         },
     *         {
     *             "value": "Y",
     *             "label": "application_vehicle-safety_safety-sub-action.data.isExternal.option.yes"
     *         }
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $isExternal = null;
}
