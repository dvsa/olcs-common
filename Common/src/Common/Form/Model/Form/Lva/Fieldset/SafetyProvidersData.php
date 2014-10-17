<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 */
class ApplicationVehicleSafetySafetySubActionData
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
    public $licence = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_safety-sub-action.data.isExternal",
     *     "value_options": {
     *         "N":
     * "application_vehicle-safety_safety-sub-action.data.isExternal.option.no",
     *         "Y":
     * "application_vehicle-safety_safety-sub-action.data.isExternal.option.yes"
     *     },
     *     "help-block": "Please choose"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $isExternal = null;


}

