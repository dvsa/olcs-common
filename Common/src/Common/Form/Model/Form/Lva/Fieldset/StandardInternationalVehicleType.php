<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

class StandardInternationalVehicleType
{
    /**
     * @Form\Name("vehicle-type")
     * @Form\Options({
     *      "error-message": "type-of-vehicle-error",
     *      "label": "application_type-of-licence_licence-type.data.vehicleType",
     *      "label_attributes": {"class": "form-control form-control--radio"},
     *      "hint": "application_type-of-licence_licence-type.data.vehicleType.hint",
     *      "value_options": {
     *          {
     *             "value":\Common\RefData::APP_VEHICLE_TYPE_LGV,
     *             "label":"select-option-yes",
     *          },
     *          {
     *             "value":\Common\RefData::APP_VEHICLE_TYPE_MIXED,
     *             "label":"select-option-no",
     *          }
     *      }
     * })
     * @Form\Type("\Common\Form\Elements\Types\Radio")
     */
    public $vehicleType = null;

    /**
     * @Form\Name("lgv-declaration-confirmation")
     * @Form\Attributes({
     *   "id": "lgv-declaration-confirmation",
     *   "data-container-class": "lgv-declaration-confirmation"
     * })
     * @Form\Options({
     *     "checked_value": "1",
     *     "unchecked_value": "0",
     *     "label": "lgv-undertakings.form.declaration.label",
     *     "must_be_value": "1",
     *     "not_checked_message": "lgv-undertakings.form.declaration.error"
     * })
     * @Form\Type("\Common\Form\Elements\InputFilters\SingleCheckbox")
     */
    public $lgvDeclarationConfirmation = null;
}
