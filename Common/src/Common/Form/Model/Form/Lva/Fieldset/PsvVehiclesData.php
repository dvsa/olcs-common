<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * Psv Vehicle Data
 */
class PsvVehiclesData
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
}
