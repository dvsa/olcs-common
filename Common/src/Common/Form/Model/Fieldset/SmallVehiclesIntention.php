<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("smallVehiclesIntention")
 * @Form\Options({
 *     "label": "application_vehicle-safety_undertakings-smallVehiclesUndertakings"
 * })
 */
class SmallVehiclesIntention
{

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.smallVehiclesIntention.yesNo",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $psvOperateSmallVhl = null;

    /**
     * @Form\Attributes({"id":"","class":"long"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.smallVehiclesIntentionDetails.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box."
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\VehicleUndertakingsOperateSmallVehicles")
     */
    public $psvSmallVhlNotes = null;

    /**
     * @Form\Attributes({"id":"","class":"long","disabled":"disabled"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box."
     * })
     * @Form\Type("\Zend\Form\Element\Textarea")
     */
    public $psvSmallVhlScotland = null;

    /**
     * @Form\Attributes({"id":"","class":"long","disabled":"disabled"})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.smallVehiclesUndertakings.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box."
     * })
     * @Form\Type("\Zend\Form\Element\Textarea")
     */
    public $psvSmallVhlUndertakings = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.smallVehiclesConfirmation",
     *     "value_options": {
     *
     *     },
     *     "help-block": "Please choose",
     *     "must_be_value": "1"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\VehicleUndertakingsOperateSmallVehiclesAgreement")
     */
    public $psvSmallVhlConfirmation = null;


}

