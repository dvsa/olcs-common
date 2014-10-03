<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("licence")
 */
class Licence
{

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.vehicleInspectionInterval",
     *     "empty_option": "Please Select",
     *     "disable_inarray_validator": false,
     *     "help-block": "Please select a category",
     *     "category": "inspection_interval_vehicle"
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
     *     "category": "inspection_interval_trailer"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $safetyInsTrailers = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.moreFrequentInspections",
     *     "value_options": "yes_no",
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
     *     "category": "tachograph_analyser"
     * })
     * @Form\Type("DynamicSelect")
     */
    public $tachographIns = null;

    /**
     * @Form\Attributes({"class":"","id":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_safety.licence.tachographAnalyserContractor"
     * })
     * @Form\Required(false)
     * @Form\Type("\Common\Form\Elements\InputFilters\VehicleSafetyTachographAnalyserContractor")
     */
    public $tachographInsName = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;


}

