<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("data")
 * @Form\Options({})
 */
class ApplicationVehicleSafetyVehiclePsvSubActionData
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
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $psvType = null;

    /**
     * @Form\Attributes({"id":"vrm","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-psv-sub-action.data.vrm",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-5",
     *     "help-block": "Between 2 and 50 characters."
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Filter({"name":"Zend\Filter\StringToUpper"})
     * @Form\Filter({
     *     "name": "Zend\Filter\PregReplace",
     *     "options": {
     *         "pattern": "/\ /",
     *         "replacement": ""
     *     }
     * })
     * @Form\Validator({"name":"Zend\Validator\StringLength","options":{"min":2,"max":7}})
     * @Form\Validator({"name":"Zend\I18n\Validator\Alnum"})
     */
    public $vrm = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-psv-sub-action.data.isNovelty",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $isNovelty = null;


}

