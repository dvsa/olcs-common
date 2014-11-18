<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("lva-safety-licence")
 */
class SafetyLicence
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.vehicleInspectionInterval",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "category": "inspection_interval_vehicle",
     *     "service_name": "StaticList"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $safetyInsVehicles = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.trailerInspectionInterval",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "category": "inspection_interval_trailer",
     *     "service_name": "StaticList"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $safetyInsTrailers = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.moreFrequentInspections",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "hint":
     * "application_vehicle-safety_safety.licence.moreFrequentInspectionsHint"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $safetyInsVaries = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_safety.licence.tachographAnalyser",
     *     "help-block": "Please choose",
     *     "category": "tachograph_analyser",
     *     "service_name": "StaticList"
     * })
     * @Form\Type("DynamicRadio")
     */
    public $tachographIns = null;

    /**
     * @Form\Required(true)
     * @Form\AllowEmpty(true)
     * @Form\Input("\Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Attributes({"class":"medium","id":"", "required": false})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.tachographAnalyserContractor"
     * })
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name": "ValidateIf",
     *      "options": {
     *          "context_field": "tachographIns",
     *          "context_values": {"tach_external"},
     *          "validators": {
     *              {"name": "NotEmpty"}
     *          }
     *     }
     * })
     */
    public $tachographInsName = null;
}
