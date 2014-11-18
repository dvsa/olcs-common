<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("smallVehiclesIntention")
 * @Form\Options({
 *     "label": "application_vehicle-safety_undertakings-smallVehiclesUndertakings"
 * })
 */
class VehiclesDeclarationsSmallVehiclesIntention
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntention.yesNo",
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
     * @Form\AllowEmpty(false)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntentionDetails.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6",
     *     "help-block": "You can type anything in this box."
     * })
     * @Form\Validator({"name": "Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesValidator"})
     * @Form\Type("Textarea")
     */
    public $psvSmallVhlNotes = null;

    /**
     * @Form\Attributes({"value":"<legend>%s</legend>"})
     * @Form\Options({
     *     "tokens": {
     *        0: "application_vehicle-safety_undertakings-smallVehiclesUndertakings"
     *     }
     * })
     * @Form\Type("\Common\Form\Elements\Types\HtmlTranslated")
     */
    public $dummyLegend = null;

    /**
     * @Form\Attributes({"id":"","class":"long","disabled":"disabled"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6"
     * })
     * @Form\Type("\Zend\Form\Element\Textarea")
     */
    public $psvSmallVhlScotland = null;

    /**
     * @Form\Attributes({"id":"","class":"long","disabled":"disabled"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakings.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6"
     * })
     * @Form\Type("\Zend\Form\Element\Textarea")
     */
    public $psvSmallVhlUndertakings = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Required(false)
     * @Form\AllowEmpty(false)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesConfirmation",
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator({
     *     "name": "Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator"
     * })
     */
    public $psvSmallVhlConfirmation = null;
}
