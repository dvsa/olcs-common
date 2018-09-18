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
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     * @Form\Options({
     *     "label": "application_vehicle-safety_safety.licence.label",
     *     "label_attributes": {"class": "form-element__question"},
     * })
     */
    public $safetyInspections = null;

    /**
     * @Form\Attributes({"class":"tiny", "id":""})
     * @Form\Options({
     *     "label":"application_vehicle-safety_safety.licence.vehicleInspectionInterval",
     *     "error-message": "safetyLicence_safetyInsVehicles-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name": "Between", "options": {
     *     "min":1, "max":13,
     *     "messages": {"notBetween": "safetyLicence_safetyInsBetween-error"}
     * }})
     */
    public $safetyInsVehicles = null;

    /**
     * @Form\Attributes({"class":"tiny", "id":""})
     * @Form\Options({
     *     "label":"application_vehicle-safety_safety.licence.trailerInspectionInterval",
     *     "error-message": "safetyLicence_safetyInsTrailers-error"
     * })
     * @Form\Type("Text")
     * @Form\Filter({"name":"Zend\Filter\StringTrim"})
     * @Form\Validator({"name": "Between", "options": {
     *     "min":1, "max":13,
     *     "messages": {"notBetween": "safetyLicence_safetyInsBetween-error"}
     * }})
     */
    public $safetyInsTrailers = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_safety.licence.moreFrequentInspections",
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "hint": "application_vehicle-safety_safety.licence.moreFrequentInspectionsHint"
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $safetyInsVaries = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "error-message": "safetyLicence_tachographsIns-error",
     *     "label": "application_vehicle-safety_safety.licence.tachographAnalyser",
     *     "label_attributes": {"class": "form-control form-control--radio"},
     *     "value_options": {
     *         {"value": "tach_internal", "label": "tachograph_analyser.tach_internal"},
     *         {"value": "tach_external", "label": "tachograph_analyser.tach_external"},
     *         {"value": "tach_na", "label": "tachograph_analyser.tach_na"}
     *     },
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
