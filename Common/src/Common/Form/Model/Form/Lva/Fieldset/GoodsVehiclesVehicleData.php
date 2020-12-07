<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("data")
 */
class GoodsVehiclesVehicleData
{
    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $id = null;

    /**
     * @Form\Attributes({"value":""})
     * @Form\Type("Hidden")
     */
    public $version = null;

    /**
     * @Form\Attributes({
     *     "class": "medium",
     *     "id": "vrm",
     *     "placeholder": "",
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.vrm",
     *     "label_attributes": {"class": "form-element__question"},
     *     "error-message": "vehicle.error.top.vrm",
     * })
     * @Form\Type("\Common\Form\Elements\Custom\VehicleVrm")
     */
    public $vrm = null;

    /**
     * @Form\Attributes({
     *     "class": "small",
     *     "id": "plated_weight",
     *     "placeholder": "",
     *     "pattern": "\d*",
     * })
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-sub-action.data.weight",
     *     "label_attributes": {"class": "form-element__question"},
     *     "error-message": "vehicle.error.top.platedWeight",
     * })
     * @Form\Type("\Common\Form\Elements\Custom\VehiclePlatedWeight")
     */
    public $platedWeight = null;
}
