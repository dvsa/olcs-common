<?php

namespace Common\Form\Model\Fieldset;

use Zend\Form\Annotation as Form;

/**
 * @codeCoverageIgnore Auto-generated file with no methods
 * @Form\Name("limousinesNoveltyVehicles")
 * @Form\Options({"label":"application_vehicle-safety_undertakings-limousines"})
 */
class LimousinesNoveltyVehicles
{

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.limousinesApplication.yesNo",
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
     *     "label":
     * "application_vehicle-safety_undertakings.limousinesApplication.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvNoLimousineConfirmationLabel = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.limousinesApplication.agreement",
     *     "value_options": {
     *
     *     },
     *     "help-block": "Please choose",
     *     "must_be_value": "1"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\VehicleUndertakingsNoLimousineConfirmation")
     */
    public $psvNoLimousineConfirmation = null;

    /**
     * @Form\Options({
     *     "label":
     * "application_vehicle-safety_undertakings.limousinesNine.agreement.label"
     * })
     * @Form\Type("\Common\Form\Elements\Types\Html")
     */
    public $psvOnlyLimousinesConfirmationLabel = null;

    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.limousinesNine.agreement",
     *     "value_options": {
     *
     *     },
     *     "help-block": "Please choose",
     *     "must_be_value": "1"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $psvOnlyLimousinesConfirmation = null;


}

