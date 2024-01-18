<?php

namespace Common\Form\Model\Form\Lva\Fieldset;

use Laminas\Form\Annotation as Form;

/**
 * @Form\Name("data")
 * @Form\Options({})
 */
class PsvVehicleData
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
     * @Form\Attributes({"id":"vrm","placeholder":""})
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-psv-sub-action.data.vrm",
     *     "error-message": "vehicle.error.top.vrm",
     * })
     * @Form\Type("\Common\Form\Elements\Custom\VehicleVrm")
     */
    public $vrm = null;

    /**
     * @Form\Options({
     *     "label": "application_vehicle-safety_vehicle-psv-sub-action.data.makeModel",
     *     "error-message": "vehicle.error.top.modelName",
     * })
     * @Form\Type("Text")
     * @Form\Required(false)
     * @Form\Filter("Laminas\Filter\StringTrim")
     * @Form\Validator("Laminas\Validator\StringLength",
     *     options={
     *          "min":2,
     *          "max":100,
     *     },
     * )
     */
    public $makeModel = null;
}
