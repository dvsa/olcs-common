<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @Form\Name("limousinesNoveltyVehicles")
 * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
 */
class VehiclesDeclarationsLimousinesNoveltyVehicles
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.yesNo",
     *     "value_options": {"Y": "Yes", "N": "No"},
     *     "help-block": "Please choose",
     *     "label_attributes": {
     *         "class": "inline"
     *     }
     * })
     * @Form\Type("\Zend\Form\Element\Radio")
     */
    public $psvLimousines = null;

    /**
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvNoLimousineConfirmationLabel = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Required(false)
     * @Form\AllowEmpty(false)
     * @Form\Input("Common\InputFilter\ContinueIfEmptyInput")
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesApplication.agreement",
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "help-block": "Please choose",
     *     "must_be_value": "T"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     * @Form\Validator({"name": "Common\Form\Elements\Validators\VehicleUndertakingsNoLimousineConfirmationValidator"})
     */
    public $psvNoLimousineConfirmation = null;

    /**
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesNine.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvOnlyLimousinesConfirmationLabel = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesNine.agreement",
     *     "checked_value": "Y",
     *     "unchecked_value": "N",
     *     "help-block": "Please choose",
     *     "must_be_value": "Y"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvOnlyLimousinesConfirmation = null;
}
