<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Vehicle Data
 */
class VehiclesData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":"","class":"js-enabled"})
     * @Form\Options({
     *     "fieldset-attributes": {
     *         "class": "checkbox inline"
     *     },
     *     "label": "application_vehicle-safety_vehicle-psv.hasEnteredReg",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $hasEnteredReg = null;

    /**
     * @Form\Type("Common\Form\Elements\Types\Html")
     * @Form\Attributes({
     *      "value":
     *      "<div id=""notice"">If you don't have vehicle details at the moment, you must tell the Central Licensing Office when they're available.</div>"})
     */
    public $notice = null;
}
