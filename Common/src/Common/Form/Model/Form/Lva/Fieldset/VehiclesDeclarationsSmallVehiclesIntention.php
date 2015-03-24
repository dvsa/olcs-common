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
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntentionDetails.title",
     *     "label_attributes": {
     *         "class": "col-sm-2"
     *     },
     *     "column-size": "sm-6"
     * })
     * @Form\Validator({"name": "Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesValidator"})
     * @Form\Type("Textarea")
     */
    public $psvSmallVhlNotes = null;

    /**
     * @Form\Attributes({
     *     "id":"",
     *     "class":"long",
     *      "value":"markup-application_vehicle-safety_undertakings-smallVehiclesUndertakingsScotland"
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.title"
     * })
     * @Form\Type("Common\Form\Elements\Types\TermsBox")
     */
    public $psvSmallVhlScotland = null;

    /**
     * @Form\Attributes({
     *     "id":"",
     *     "class":"long",
     *     "value": "markup-application_vehicle-safety_undertakings-smallVehiclesUndertakings"
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakings.title"
     * })
     * @Form\Type("Common\Form\Elements\Types\TermsBox")
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
     *     "unchecked_value": "N"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator({
     *     "name": "Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator"
     * })
     */
    public $psvSmallVhlConfirmation = null;
}
