<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("smallVehiclesIntention")
 * @Form\Options({
 *     "label": "application_vehicle-safety_undertakings-smallVehiclesUndertakings",
 * })
 * @Form\Attributes({
 *     "class": "psv-show-small psv-show-both"
 * })
 */
class VehiclesDeclarationsSmallVehiclesIntention
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntention.yesNo",
     *     "legend-attributes": {"class": "form-element__label psv-small-vh-section"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "value_options": {"Y": "Yes", "N": "No"}
     * })
     * @Form\Type("\Laminas\Form\Element\Radio")
     */
    public $psvOperateSmallVhl;

    /**
     * @Form\Attributes({"id":"","class":"long"})
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "legend-attributes": {"class": "form-element__label"},
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntentionDetails.title"
     * })
     * @Form\Validator("Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesValidator")
     * @Form\Type("Textarea")
     */
    public $psvSmallVhlNotes;

    /**
     * @Form\Attributes({
     *     "id":"", "value":"markup-application_vehicle-safety_undertakings-smallVehiclesUndertakingsScotland"
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakingsScotland.title"
     * })
     * @Form\Type("Common\Form\Elements\Types\PlainText")
     */
    public $psvSmallVhlScotland;

    /**
     * @Form\Attributes({
     *     "value": "markup-application_vehicle-safety_undertakings-smallVehiclesUndertakings",
     *     "data-container-class":"form-control__fieldset-title"
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesUndertakings.title"
     * })
     * @Form\Type("Common\Form\Elements\Types\PlainText")
     */
    public $psvSmallVhlUndertakings;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Required(false)
     * @Form\AllowEmpty(false)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesConfirmation",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--advanced"},
     *     "checked_value": "Y",
     *     "unchecked_value": "N"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator( "Common\Form\Elements\Validators\VehicleUndertakingsOperateSmallVehiclesAgreementValidator")
     */
    public $psvSmallVhlConfirmation;
}
