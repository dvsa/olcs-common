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
class VehiclesDeclarationsOperatingSmallVehicle
{
    /**
     * @Form\Attributes({"id":"","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_undertakings.smallVehiclesIntention.yesNo",
     *     "legend-attributes": {"class": "form-element__label"},
     *     "label_attributes": {"class": "form-control form-control--radio form-control--inline"},
     *     "value_options": {"Y": "Yes", "N": "No"}
     * })
     * @Form\Type("\Laminas\Form\Element\Radio")
     */
    public $psvOperateSmallVhl;
}
