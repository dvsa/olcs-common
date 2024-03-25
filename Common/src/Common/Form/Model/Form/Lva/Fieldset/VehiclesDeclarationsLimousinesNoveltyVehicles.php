<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("limousinesNoveltyVehicles")
 * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
 * @Form\Attributes({
 *      "class": "psv-show-small psv-show-large psv-show-both"
 * })
 */
class VehiclesDeclarationsLimousinesNoveltyVehicles
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.yesNo",
     *     "legend-attributes": {"class": "form-element__label"},
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"}
     * })
     * @Form\Type("\Laminas\Form\Element\Radio")
     */
    public $psvLimousines;

    /**
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvNoLimousineConfirmationLabel;

    /**
     * @Form\Attributes({"class": "js-no-confirmation"})
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.agreement",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--advanced"},
     *     "checked_value": "Y",
     *     "unchecked_value": "N"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator("Common\Form\Elements\Validators\VehicleUndertakingsNoLimousineConfirmationValidator",
     *     options={"required_context_value": "N"}
     * )
     */
    public $psvNoLimousineConfirmation;

    /**
     * @Form\Attributes({"data-container-class":"psv-show-large psv-show-both"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesNine.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvOnlyLimousinesConfirmationLabel;

    /**
     * @Form\Attributes({"class": "js-only-confirmation", "data-container-class":"psv-show-large psv-show-both"})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesNine.agreement",
     *     "label_attributes": {"class": "form-control form-control--checkbox form-control--advanced"},
     *     "checked_value": "Y",
     *     "unchecked_value": "N"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator("Common\Form\Elements\Validators\VehicleUndertakingsNoLimousineConfirmationValidator",
     *     options={
     *         "required_context_value": "Y"
     *     }
     * )
     */
    public $psvOnlyLimousinesConfirmation;
}
