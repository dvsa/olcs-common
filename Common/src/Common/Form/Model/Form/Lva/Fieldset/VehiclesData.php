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
     *      "<div id=""notice"">If the vehicle details are not available at this time you <b>must</b>
     *       inform the central licensing office when the details are available</div>"})
     */
    public $notice = null;
}
