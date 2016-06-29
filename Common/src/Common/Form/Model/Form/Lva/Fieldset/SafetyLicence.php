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
     *     "label": "application_vehicle-safety_safety.licence.vehicleInspectionInterval",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "value_options": {
     *         "1": "1 {Week}",
     *         "2": "2 {Weeks}",
     *         "3": "3 {Weeks}",
     *         "4": "4 {Weeks}",
     *         "5": "5 {Weeks}",
     *         "6": "6 {Weeks}",
     *         "7": "7 {Weeks}",
     *         "8": "8 {Weeks}",
     *         "9": "9 {Weeks}",
     *         "10": "10 {Weeks}",
     *         "11": "11 {Weeks}",
     *         "12": "12 {Weeks}",
     *         "13": "13 {Weeks}"
     *     }
     * })
     * @Form\Type("Select")
     */
    public $safetyInsVehicles = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_safety.licence.trailerInspectionInterval",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "value_options": {
     *         "1": "1 {Week}",
     *         "2": "2 {Weeks}",
     *         "3": "3 {Weeks}",
     *         "4": "4 {Weeks}",
     *         "5": "5 {Weeks}",
     *         "6": "6 {Weeks}",
     *         "7": "7 {Weeks}",
     *         "8": "8 {Weeks}",
     *         "9": "9 {Weeks}",
     *         "10": "10 {Weeks}",
     *         "11": "11 {Weeks}",
     *         "12": "12 {Weeks}",
     *         "13": "13 {Weeks}",
     *         "0": "N/A"
     *     }
     * })
     * @Form\Type("Select")
     */
    public $safetyInsTrailers = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox inline"
     *      },
     *     "label": "application_vehicle-safety_safety.licence.moreFrequentInspections",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     },
     *     "hint": "application_vehicle-safety_safety.licence.moreFrequentInspectionsHint"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $safetyInsVaries = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *      "fieldset-attributes": {
     *          "class": "checkbox"
     *      },
     *     "error-message": "safetyLicence_tachographsIns-error",
     *     "label": "application_vehicle-safety_safety.licence.tachographAnalyser",
     *     "value_options": {
     *         {
     *             "value": "tach_internal",
     *             "label": "tachograph_analyser.tach_internal",
     *             "label_attributes": {
     *                 "aria-label": "Who analyses your tachograph data? An owner or employee of the business"
     *             }
     *         },
     *         {
     *             "value": "tach_external",
     *             "label": "tachograph_analyser.tach_external"
     *         },
     *         {
     *             "value": "tach_na",
     *             "label": "tachograph_analyser.tach_na"
     *         }
     *     },
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
     *     "label": "application_vehicle-safety_safety.licence.tachographAnalyserContractor",
     *     "error-message": "You must add at least one safety inspector"
     * })
     * @Form\Type("Text")
     * @Form\Validator({
     *      "name": "ValidateIf",
     *      "options": {
     *          "context_field": "tachographIns",
     *          "context_values": {"tach_external"},
     *          "validators": {
     *              {"name": "NotEmpty"},
     *              {"name": "StringLength", "options": {"min":"1","max":90}}
     *          }
     *     }
     * })
     */
    public $tachographInsName = null;
}
